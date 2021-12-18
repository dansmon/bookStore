<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\rentBook;
use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class RentBookController extends Controller
{
    // ** Pridobi vse uporabnike, ki obstajajo
    public function userLookup()
    {
        $users = User::orderBy('name', 'ASC')
            ->select(['id', 'name', 'emso'])
            ->get();
        return $users;
    }

    // ** podatki za tabelo, ki prikazuje knjige v rezervaciji / izposoji
    public function resRenData()
    {
        // ** Glede na prijavljenega uporabnika se pokažejo rezervacije / izposoje. Če je admin oz knjižničar se pokažejo rezervacije / izposoje od vseh, sicer pa lahko navadni uporabniki vidijo samo svoje
        $filter = "rentbook.id_member < 0";
        if (auth()->user()->is_admin)
            $filter = "rentbook.id_member >= 0";
        if (auth()->user()->is_user)
            $filter = "rentbook.id_member = " . strval(auth()->user()->id);

        /* 
        + Prikažejo se rezervacije / izposoje, ki:
            - niso v statusu 0 (predhodno končana rezervacija / izposoja) in je trenutni datum manjši od datuma do katerega je rezervacija / izposoja
            - so trenutno v izposoji (status 2 ali 3), trenutni datum pa že presega datum do katerega je bila opravljena rezervacija oz. izposoja, kar pomeni, da knjiga še ni bila vrnjena
            - knjige, ki so v izposoji, vendar trenutni datum presega rok datuma do katerega bi morale biti vrnjene, takšne knjige se prikažejo z informacijo o prekoračitvi
            - p_renting vsebuje informacijo o tem ali je pri tej knjigi že možen prehod v stanje izposoje iz stanja rezervacija      
        */
        $titles = rentBook::orderBy('id', 'ASC')
            ->select([
                'rentbook.id', 'rentbook.id_ident', 'rentbook.id_zap', 'rentbook.id_member', 'book.naslov', 'rentbook.status', 'users.name',
                rentBook::raw('
                    CASE WHEN rentbook.datum_do < CURRENT_DATE AND (rentbook.status = 2 OR rentbook.status = 3) 
                        THEN 1
                        ELSE 0 
                    END
                    as prekoracitev'),
                rentBook::raw('
                    CASE WHEN rentbook.datum_od <= CURRENT_DATE AND rentbook.datum_do > CURRENT_DATE 
                        THEN 1
                        ELSE 0 
                    END
                    as p_renting'),
                rentBook::raw('concat(rentbook.id_ident, " / ", rentbook.id_zap) as fn_izvod'),
                rentBook::raw('DATE_FORMAT(rentbook.datum_od, "%d.%m.%Y") as datum_od'),
                rentBook::raw('DATE_FORMAT(rentbook.datum_do, "%d.%m.%Y") as datum_do')
            ])
            ->where(function ($query) use ($filter) {
                $query
                    ->where('rentbook.datum_do', '>=', rentBook::raw('CURRENT_DATE'))
                    ->where('rentbook.status', '<>', '0')
                    ->whereRaw($filter);
            })
            ->orWhere(function ($query) use ($filter) {
                $query
                    ->where('rentbook.datum_do', '<', rentBook::raw('CURRENT_DATE'))
                    ->where(function ($query2) {
                        $query2
                            ->where('rentbook.status', '=', '2')
                            ->orWhere('rentbook.status', '=', '3');
                    })
                    ->whereRaw($filter);
            })
            // ** povezava z drugimi tabelami
            ->join('book', function ($join) {
                $join->on('book.ident', '=', 'rentbook.id_ident')->on('book.zap', '=', 'rentbook.id_zap');
            })
            ->join('users as users', 'rentbook.id_member', '=', 'users.id')
            ->get();

        if (count($titles) == 0) {
            return null;
        }

        return $titles;
    }

    // ** Za vsako knjigo poiščemo podknjige za katere prikažemo proste termine, zasedenost, možnost izposoje in rezervacije
    public function bookSubBooks($polIdent, $obdobjeDatum, $casRezIzp)
    {
        //| -------------------------------------------------------------------- //
        // ** iz frontenda dobimo željeno število dni rezervacije ** 
        $FE_stDniRezIzp = $casRezIzp;

        // ** iz frontenda se dobi mesec oz datum kdaj bi rezervacijo želeli **
        $FE_mesecRezIzp = date($obdobjeDatum);

        // ** AI = additional info - o vsaki knjigi še dodatne informacije o prostih terminih ** 
        $booksAI = array();

        // ** prvi dan podanega meseca **
        $prviDanMesec = date(date("Y", strtotime(date($FE_mesecRezIzp))) . '-' . date("m", strtotime(date($FE_mesecRezIzp))) . '-' . '01');
        //| -------------------------------------------------------------------- //


        // ** vrne vse knjige z določenim naslovom ** 
        $books = Book::orderBy('naslov', 'ASC')
            ->select(['book.naslov', 'book.leto_izdaje', 'book.avtor', 'book.kategorija', 'book.ident', 'book.zap'])
            ->where('book.ident', '=', $polIdent)
            ->get();

        // ** za vsak izvod določene knjige **
        for ($x = 0; $x < count($books); $x++) {

            //| -------------------------------------------------------------------- //
            // ** polje v katerem so vpisane rezervacije/izposoje, prosti dnevi in dnevi možni za rezervacijo glede na prosta mesta v odvisnosti od št. dni rezervacije / izposoje **
            $mergedReservation = array();

            // ** polje, ki vsebuje dneve za željen datum in informacijo za vsak dan ali je knjiga zasedena, možna rezervacije  **
            $di_tipDnevi = array();

            // ** zadnji dan, ki je v željenem datumu (front-end)**
            $zadnjiDanMesecFE = date("Y-m-t", strtotime($FE_mesecRezIzp));

            // ** zadnji dan, ki je v trenutnem mesecu **
            $zadnjiDanMesecNOW = date("Y-m-t", strtotime(date('Y-m-d')));

            // ** število dni v mesecu željenega datuma (front-end) **
            $stDniMesecFE = cal_days_in_month(CAL_GREGORIAN, date("m", strtotime($FE_mesecRezIzp)), date("Y", strtotime($FE_mesecRezIzp)));

            // ** mozni dnevi v mesecu, ki jih lahko rezerviramo **
            $mozniTermini = array();

            // ** koliko terminov je možno rezervirati v odvisnosti dolžine rezervacije na dneve, kadar je to možno **
            $mozniMaxTermini = array();

            // ** true: če kakšna rezervacija obstaja **
            $b_reservation = false;
            //| -------------------------------------------------------------------- //

            // ** izbere vse datume OD - DO iz rezervacije in izposoje, ki so vezani na določeno pod-knjigo z istim naslovom **
            $dates = rentBook::orderBy('datum_od', 'ASC')
                ->select([
                    'rentbook.datum_od', 'rentbook.datum_do', 'rentbook.id_ident',
                    rentBook::raw('
                        CASE WHEN rentbook.datum_do < CURRENT_DATE AND (rentbook.status = 2 OR rentbook.status = 3) 
                            THEN 1
                            ELSE 0 
                        END
                        as prekoracitev'),
                ])
                //| ->where('rentbook.datum_do', '>=', $zeljeniMesec)
                //| ->where('rentbook.datum_do', '>=', rentBook::raw('CURRENT_DATE'))
                ->where('rentbook.id_ident', '=', $books[$x]->ident)
                ->where('rentbook.id_zap', '=', $books[$x]->zap)
                ->where('rentbook.status', '<>', '0')
                ->get();

            // ** Če je prekoračitev, se nadaljne ne bo preverjalo možnih terminov    
            $boolPrekoracitev = false;
            foreach ($dates as $date) {
                if ($date->prekoracitev == 1)
                    $boolPrekoracitev = true;
            }

            // ** rezervira polje 62 mest in jim kot default določi 'P' (kasneje se v teh poljih nadomesti "X" za dneve, ki je knjiga zasedena) **           
            for ($y = 1; $y <= 62; $y++) {
                $mergedReservation[$y] = 'P';
            }

            // ** za določeno podknjigo oz. izvod vsak termin OD - DO zasedenost vnese v polje, ki je rezervirano z 62 mesti **
            foreach ($dates as $date) {

                // ** če datum_od vsebuje mesec, ki je že pretekel, potem beleži zasedene dneve od 01. aktualnega meseca do datuma_do **
                if (date($date->datum_od) < $prviDanMesec) {

                    if (date($date->datum_do) >= $prviDanMesec) {
                        $days_between = ceil(abs(strtotime($date->datum_do) - strtotime($prviDanMesec)) / 86400);

                        for ($k = 1; $k <= $days_between + 1; $k++) {
                            $mergedReservation[$k] = 'X';
                        }
                    }
                }

                // ** če je datum_od beležen v prihodnjih mesecih, potem tisti dan, ki se rezervacija začne, vpišemo v polje (zaradi preračunavanja zmožnosti rezerviranja / izposoje) **
                else if (date($date->datum_od) > $zadnjiDanMesecFE) {
                    $days_between = ceil(abs(strtotime($date->datum_od) - strtotime(date("Y-m-d", strtotime($FE_mesecRezIzp . "first day of +1 month")))) / 86400);
                    $mergedReservation[($days_between + $stDniMesecFE) + 1] = 'X';
                }

                // ** če je datum_od v aktualnem mesecu, potem se preračuna koliko dni traja rezervacija in jo ustrezno zabeleži v polje zasedenosti **
                else {
                    $days_between = ceil(abs(strtotime($date->datum_do) - strtotime($date->datum_od)) / 86400);

                    for ($k = intval(date("d", strtotime($date->datum_od))); $k <= $days_between + date("d", strtotime($date->datum_od)); $k++) {
                        $mergedReservation[$k] = 'X';
                    }
                }
            }

            // ** glede na željeno število dni rezervacije se preveri kateri dnevi so možni za rezervacijo **
            for ($w = 1; $w < count($mergedReservation); $w++) {
                if ($boolPrekoracitev == false) {
                    if ($mergedReservation[$w] == 'P') {
                        $terminOK = true;
                        if ($FE_stDniRezIzp + $w <= count($mergedReservation)) {
                            for ($q = $w; ($q < $FE_stDniRezIzp + $w); $q++) {
                                if ($mergedReservation[$q] == 'X') {
                                    $terminOK = false;
                                    break;
                                }
                            }
                        } else {
                            $terminOK = false;
                        }

                        if ($terminOK == true) {
                            if (date($FE_mesecRezIzp) > $zadnjiDanMesecNOW) {
                                $mergedReservation[$w] = 'R';
                            } else {
                                if ($w >= (date('d'))) {
                                    $mergedReservation[$w] = 'R';
                                }
                            }
                            $mozniTermini[] = $w;
                        }
                    }
                }
            }

            // ** stevilo dni moznih rezervacij se izračuna za aktualen mesec od dejanskega dneva do konca meseca, pri mesecu po izboru pa se izračuna za celoten mesec **    
            $stDniMozneRez = ($stDniMesecFE) - ($FE_stDniRezIzp - 1);

            // mesec po izboru, ki ni aktualen
            if (date($FE_mesecRezIzp) > $zadnjiDanMesecNOW) {
                $stDniMozneRez = 0;
                foreach ($mozniTermini as $termin) {
                    if ($termin <= $stDniMesecFE) {
                        $stDniMozneRez++;
                    }
                }
            }
            // aktualen mesec 
            else {
                $stDniMozneRez = 0;
                foreach ($mozniTermini as $termin) {
                    if ($termin >= date('d') && $termin <= $stDniMesecFE) {
                        $stDniMozneRez++;
                    }
                }
            }

            // ** preračunamo max število rezervacij **
            $stMozRez_mesec = ($stDniMesecFE) / $FE_stDniRezIzp;

            for ($h = 1; $h <= $stDniMesecFE; $h++) {
                if ($mergedReservation[$h] == 'R') {
                    $terminOK = true;

                    for ($g = $h; ($g < $FE_stDniRezIzp + $h); $g++) {
                        if ($mergedReservation[$g] == 'X') {
                            $terminOK = false;
                            break;
                        }
                    }

                    if ($terminOK == true) {
                        $mozniMaxTermini[] = $h;
                        $h = $h + $FE_stDniRezIzp - 1;
                    }
                }
            }

            // ** % zasedenosti - > barvni indikator zasedenosti
            $di_indikator = round((100 / ceil($stMozRez_mesec)) * (count($mozniMaxTermini)));
            $di_stTermins = count($mozniMaxTermini) . ' / ' . ceil($stMozRez_mesec);
            $di_indikatorStopnje = 0;

            if ($di_indikator == 0)
                $di_indikatorStopnje = 1;
            else if ($di_indikator == 100)
                $di_indikatorStopnje = 5;


            else if ($di_indikator > 0 && $di_indikator <= 30)
                $di_indikatorStopnje = 2;
            else if ($di_indikator > 30 && $di_indikator <= 60)
                $di_indikatorStopnje = 3;
            else if ($di_indikator > 60 && $di_indikator < 100)
                $di_indikatorStopnje = 4;


            // ** za prikaz napolni polje z določenimi vrednostmi ** --> X = zasedeno, P = prosto, R = moznost rezervacije / izposoje 
            for ($v = 1; $v <= $stDniMesecFE; $v++) {
                $di_tipDnevi[] = $mergedReservation[$v];
            }

            // ** ali se lahko knjiga izposodi **
            $moznostIzposoje = 0;
            if ($FE_mesecRezIzp <= $zadnjiDanMesecNOW) {
                if ($mergedReservation[intval(date('d'))] == 'R') {
                    $moznostIzposoje = 1;
                }
            }

            // ** Nastavimo ime meseca **
            $month = array(
                1 => 'Januar',
                2 => 'Februar',
                3 => 'Marec',
                4 => 'April',
                5 => 'Maj',
                6 => 'Junij',
                7 => 'Julij',
                8 => 'Avgust',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'December',
            );

            $book = array(
                'naslov' => $books[$x]->naslov,
                'leto_izdaje' => $books[$x]->leto_izdaje,
                'avtor' => $books[$x]->avtor,
                'kategorija' => $books[$x]->kategorija,
                'ident' => $books[$x]->ident,
                'zap' => $books[$x]->zap,
                'stDniMesec' => $stDniMesecFE,
                'pod_dniRazp' =>  $stDniMozneRez . ' / ' . $stDniMesecFE, // št dni na razpolago / število dni prazen mesec 
                'pod_termRazp' => $di_stTermins,
                'st_terminov' => count($mozniMaxTermini),
                'st_dniRazp' => $stDniMozneRez,
                'indikator' => $di_indikatorStopnje,
                'zasedenostDnevi' => $di_tipDnevi,
                'pRenting' => $moznostIzposoje,
                'monthName' => $month[intval(date('m', strtotime($FE_mesecRezIzp)))],
                'r_datumOD' => "",
                'r_datumDO' => "",
                "b_reservation" => $b_reservation,
                "prekoracitev" => $boolPrekoracitev
            );

            array_push($booksAI, $book);
            $returnJsn = json_encode($booksAI);
        }

        return $returnJsn;
    }

    // ** pridobi podatke od sprotnem nalaganju knjig od - do iz baze, ter podatke o filtrih. Sestavijo se podatki o tem, ali je določeno knjigo mogoče rezervirati oz izposoditi **
    public function loadingBooks($od_, $do_, $date_, $duration_, $title_, $author_, $year_, $category_, $mode, $reloadIdent_)
    {

        // ** sestava filtra, ki se upošteva v queryju
        $filter = "book.id >= 0";
        ($title_ != "null") ? $filter = $filter . " AND " . "book.naslov LIKE '%" . $title_ . "%'" : $filter = $filter . "";
        ($author_ != "null") ? $filter = $filter . " AND " . "book.avtor LIKE '%" . $author_ . "%'" : $filter = $filter . "";
        ($year_ != "null") ? $filter = $filter . " AND " . "book.leto_izdaje = " . $year_ : $filter = $filter . "";
        ($category_ != "null") ? $filter = $filter . " AND " . "book.kategorija LIKE '%" . $category_ . "%'" : $filter = $filter . "";

        $books = array();

        //** pridobi knjige z določenim naslovom, ki ustrezajo kriterijem (upošteva že naložene knjige pri samodejnem nalaganju novih ko pridemo do konca strani)
        if ($mode == "list") {
            $books = Book::orderBy('naslov', 'ASC')
                ->select(['book.naslov', 'book.leto_izdaje', 'book.avtor', 'book.kategorija', 'book.ident', 'book.bookImage', Book::raw('COUNT(book.ident) AS st_knjig')])
                ->whereRaw($filter)
                ->groupBy(['book.ident'])
                ->skip($od_)
                ->take($do_)
                ->get();
        }
        //** pridobi knjige z določenim naslovom, ki ustrezajo kriterijem
        else if ($mode == "book") {
            $books = Book::orderBy('naslov', 'ASC')
                ->select(['book.naslov', 'book.leto_izdaje', 'book.avtor', 'book.kategorija', 'book.ident', 'book.bookImage', Book::raw('COUNT(book.ident) AS st_knjig')])
                ->where('book.ident', '=', $reloadIdent_)
                ->get();
        }

        if (count($books) == 0) {
            $returnJsn = null;
        }


        $booksAI = array();

        // ** za vsako knjigo, ki ustreza kriterijem poiščemo izvode knjig
        for ($x = 0; $x < count($books); $x++) {

            // ** informacije o tem ali ima knjiga za določen termin možno kakšno rezervacijo in izposojo (omogočanje gumba )
            $p_renting = false;
            $p_reservation = false;
            $min_renDay = 100;
            $temp_ident = "";
            $temp_zap = "";


            // ** priprava skupnega polja
            $mergedReservation = array();
            for ($y = 1; $y <= 62; $y++) {
                $mergedReservation[$y] = 'P';
            }

            // ** klic funkcije, ki nam vrne informacije za določen izvod knjige
            $subBooks = json_decode($this->bookSubBooks($books[$x]->ident, $date_, $duration_));

            // ** za vsak določen izvod knjige preverimo ali je možna kakšna knjiga za izposojo na trenutni dan, ter pregled možnih terminov vseh izvodov
            for ($y = 0; $y < count($subBooks); $y++) {
                if ($subBooks[$y]->pRenting == '1') {
                    $p_renting = true;

                    // ** iskanje izvoda z najmanjšim izborom prostih dni za možne izposoje
                    if ($subBooks[$y]->st_dniRazp < $min_renDay) {
                        $min_renDay = $subBooks[$y]->st_dniRazp;
                        $temp_ident = $subBooks[$y]->ident;
                        $temp_zap = $subBooks[$y]->zap;
                    }
                }
                // ** združitev skupnih terminov možnih rezervacij, ter podatek o tem ali je sploh možna rezervacija
                for ($c = 0; $c < count($subBooks[$y]->zasedenostDnevi); $c++) {
                    if ($subBooks[$y]->zasedenostDnevi[$c] == 'R') {
                        $mergedReservation[$c + 1] = 'R';
                        $p_reservation = true;
                    }
                }
            }

            // ** iz frontenda se dobi mesec oz datum kdaj bi rezervacijo želeli **
            $FE_mesecRezIzp = date($date_); // ? bere iz vnosa 
            // ** število dni v mesecu željenega datuma (front-end) **
            $stDniMesecFE = cal_days_in_month(CAL_GREGORIAN, date("m", strtotime($FE_mesecRezIzp)), date("Y", strtotime($FE_mesecRezIzp)));

            $di_tipDnevi = array();

            // ** v polje se shrani toliko podatkov kot je število dni v mesecu
            for ($v = 1; $v <= $stDniMesecFE; $v++) {
                $di_tipDnevi[] = $mergedReservation[$v];
            }
            //auth()->user() == null
            $book = array(
                'naslov' => $books[$x]->naslov,
                'leto_izdaje' => $books[$x]->leto_izdaje,
                'avtor' => $books[$x]->avtor,
                'kategorija' => $books[$x]->kategorija,
                'ident' => $books[$x]->ident,
                'zap' => $books[$x]->zap,
                'p_renting' => (auth()->user() != null) ? $p_renting : null,
                'p_reservation' => (auth()->user() != null) ? $p_reservation : null,
                'rec_rent_ident' => (auth()->user() != null) ? $temp_ident : null,
                'rec_rent_zap' => (auth()->user() != null) ? $temp_zap : null,
                'p_resBook' => (auth()->user() != null) ? $di_tipDnevi : null,
                'bookImage' => $books[$x]->bookImage
            );

            array_push($booksAI, $book);
            $returnJsn = json_encode($booksAI);
        }

        return $returnJsn;
    }

    // ** vrne identifikacijo knjige, ki je najprimernejša za rezervacijo (je najbolj zasedena)
    public function bookIdeRes($ident, $date, $duration, $dayPicked)
    {
        // ** klic funkcije, ki vrne podatke o zasedenosti oz. nezasedenosti izvodov določene knjige v določenem mesecu za določeno obdobje od - do
        $subBooks = json_decode($this->bookSubBooks($ident, $date, $duration));

        $min_resDay = 100;
        $temp_zap = "";

        // ** iskanje knjige, ki ima na voljo najmanj možnih dni za rezervacijo (zato, da čimbolje izkoristimo določeno knjigo)
        for ($y = 0; $y < count($subBooks); $y++) {
            if ($subBooks[$y]->zasedenostDnevi[$dayPicked - 1] == "R") {

                if ($subBooks[$y]->st_dniRazp < $min_resDay) {
                    $min_resDay = $subBooks[$y]->st_dniRazp;
                    $temp_zap = $subBooks[$y]->zap;
                }
            }
        }

        return $temp_zap;
    }

    // ** Shrani rezervacijo oz izposojo
    public function store(Request $request)
    {
        $f_datumOD = date('Y-m-d', strtotime($request->AddResRent["datum_od"]));
        $f_datumDO = date('Y-m-d', strtotime($request->AddResRent["datum_do"]));

        $rentbook = new rentBook;
        $rentbook->id_ident = $request->AddResRent["id_ident"];
        $rentbook->id_zap = $request->AddResRent["id_zap"];
        $rentbook->id_member = $request->AddResRent["id_member"];
        $rentbook->datum_od = $f_datumOD;
        $rentbook->datum_do = $f_datumDO;
        $rentbook->status = $request->AddResRent["status"];

        $rentbook->save();

        return $rentbook;
    }

    // ** Prehodi statusov rezervacije / izposoje
    public function update(Request $request, $id)
    {
        $existingItem = rentBook::find($id);
        if ($existingItem) {
            //? status knjige -> 0-rezervacija oz izposoja končana; 1-rezervirana; 2-rezervacija in izposoja, 3-izposoja
            //? resRent -> 01 = konča rezervacijo, 02 = konča izposojo (če je knjiga bila rezervirana potem gre v status rezervacije - 1, sicer v status 0), 03 = iz rezervacije v izposojo (status 2) 
            if ($request->resRent == '01') {
                $existingItem->status = 0;
                $existingItem->updated_at = Carbon::now();
                $existingItem->save();
            } else if ($request->resRent == '02') {
                if ($request->status == 3) {
                    $existingItem->status = 0;
                    $existingItem->updated_at = Carbon::now();
                    $existingItem->save();
                } else {
                    $existingItem->status = 1;
                    $existingItem->updated_at = Carbon::now();
                    $existingItem->save();
                }
            } else if ($request->resRent == '03') {
                $existingItem->status = 2;
                $existingItem->updated_at = Carbon::now();
                $existingItem->save();
            }
        }

        return null;
    }

    public function destroy()
    {
    }
}
