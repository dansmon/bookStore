<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Log;
use App\Models\rentBook;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class BookController extends Controller
{
    // ** Pridobi imena slik, ki se nahajajo v mapi public/bookImages 
    public function bookPicLookup()
    {
        $booksPic = array();

        $filesName = File::files(public_path('\bookImages'));

        foreach ($filesName as $fileName) {
            $picName = array(
                'namePic' => $fileName->getFilename()
            );
            array_push($booksPic, $picName);
        }

        $returnJsn = json_encode($booksPic);

        return $returnJsn;
    }

    // ** Pridobi vse knjige z različnim identi (vsaka knjiga določene vrste ima enak "ident" vendar različno številko "zap", ki jo ločuje po izvodih)
    public function booksData()
    {
        $books = Book::orderBy('naslov', 'ASC')
            ->select(['book.naslov', 'book.leto_izdaje', 'book.avtor', 'book.kategorija', 'book.ident', Book::raw('COUNT(book.ident) AS st_knjig')])
            ->groupBy(['book.ident'])
            ->get();

        return $books;
    }

    // ** Pridobitev izvodov določene knjige ter informacije o izposojah in rezervacijah
    public function subBooks($ident)
    {
        // ** AI = additional info - o vsaki knjigi še dodatne informacije o možnosti izbrisa ** 
        $booksAI = array();

        // ** pridobi vse knjige določenega "identa" **
        $books = Book::orderBy('ident', 'ASC')
            ->select(['book.naslov', 'book.ident', 'book.zap'])
            ->where('book.ident', '=', $ident)
            ->get();

        $returnJsn = "";

        // ** za vsak izvod knjige se bo preverilo v rezervacijah in izposojah, ali je možno knjigo brez težav odstraniti, ali pa jo bo potrebno odstraniti tako, da se pobrišejo tudi aktivne rezervacije oz izposoje **
        for ($x = 0; $x < count($books); $x++) {
            $b_ident = $books[$x]->ident;
            $b_zap = $books[$x]->zap;

            // ? 0- rezervacija oz izposoja končana; 1-rezervirana; 2-rezervacija in izposoja, 3-izposoja
            $q_resRen = rentBook::orderBy('datum_od', 'ASC')
                ->select(['rentbook.datum_od', 'rentbook.datum_do', 'rentbook.id_ident', 'rentbook.id_zap', 'rentbook.status'])
                // ** where - > vse aktivne rezervacije in izposoje (kjer je datum do kdaj je izposojena večji ali enak današnjemu) in status 0 (status 0 = predhodno zaključena rezervacija / izposoja)
                ->where(function ($query) use ($b_ident, $b_zap) {
                    $query->where('rentbook.datum_do', '>=', rentBook::raw('CURRENT_DATE'))
                        ->where('rentbook.id_ident', '=', $b_ident)
                        ->where('rentbook.id_zap', '=', $b_zap)
                        ->where('rentbook.status', '<>', '0');
                })
                // ** or where - > vse zamudne izposoje glede datuma do kdaj je lahko v izposoji, vendar je knjiga še vedno v izposoji, kar pomeni, da je član knjižnice ni vrnil
                ->orWhere(function ($query) use ($b_ident, $b_zap) {
                    $query->where('rentbook.datum_do', '<', rentBook::raw('CURRENT_DATE'))
                        ->where('rentbook.id_ident', '=', $b_ident)
                        ->where('rentbook.id_zap', '=', $b_zap)
                        ->where(function ($query2) {
                            $query2->where('rentbook.status', '=', '2')
                                ->orWhere('rentbook.status', '=', '3');
                        });
                })
                ->get();

            $status_izposoja = false;
            $status_rezervacija = false;

            // ? 0- rezervacija oz izposoja končana; 1-rezervirana; 2-rezervacija in izposoja, 3-izposoja
            // ** Preveri, če ima knjiga aktivno izposojo / rezervacijo
            for ($y = 0; $y < count($q_resRen); $y++) {

                if ($q_resRen[$y]->status == '1' || $q_resRen[$y]->status == '2')
                    $status_rezervacija = true;
                if ($q_resRen[$y]->status == '2' || $q_resRen[$y]->status == '3')
                    $status_izposoja = true;
            }

            // ** Če ima knjiga ima aktivne rezervacije oz. izposoje, potem to kot podatek shranimo v "btn_color = true". Na frontendu se ta podatek o brisanju preveri in ustrezno pokaže modalno okno z opozorilom
            $btn_color = false;
            if ($status_izposoja == true || $status_rezervacija == true)
                $btn_color = true;

            // ** informacije shranimo v array "book", ki ga nato shranimo v array "booksAI" in pretvorimo tako, da ga lahko kot json pošljemo na frontend
            $book = array(
                'naslov' => $books[$x]->naslov,
                'ident' => $books[$x]->ident,
                'zap' => $books[$x]->zap,
                'status_izposoja' => $status_izposoja,
                'status_rezervacija' => $status_rezervacija,
                'btn_color' => $btn_color
            );

            array_push($booksAI, $book);
            $returnJsn = json_encode($booksAI);
        }

        // ** Podatek vrnemo samo, če je prijavljen uporabnik admin (knjižničar)
        if (auth()->user()->is_admin) {
            return $returnJsn;
        }
    }

    // ** Shranjevanje knjige (vključeni validatorji polj)
    public function store(Request $request)
    {
        $validOK = true;

        // ** Validator pri shranjevanju nove knjige v bazo
        $validator = array(
            'v_naslov' => false,
            'v_avtor' => false,
            'v_leto' => false,
            'v_kategorija' => false,
            'v_stevilo' => false,
            'v_ident' => false
        );

        // ** polje je prazno
        if ($request->AddBook["naslov"] == '')
            $validator['v_naslov'] = "Polje je obvezno";

        if ($request->AddBook["avtor"] == '')
            $validator['v_avtor'] = "Polje je obvezno";

        if ($request->AddBook["kategorija"] == '')
            $validator['v_kategorija'] = "Polje je obvezno";


        // ** polje za leto izzida knjige
        if ($request->AddBook["leto_izdaje"] == '')
            $validator['v_leto'] = "Polje je obvezno";
        else if (is_numeric($request->AddBook["leto_izdaje"]) == false)
            $validator['v_leto'] = "Polje mora vsebovati samo številke";
        else if (strlen($request->AddBook["leto_izdaje"]) != 4)
            $validator['v_leto'] = "Nepravilen vnos";
        else if ((int)$request->AddBook["leto_izdaje"] < 1000 || (int)$request->AddBook["leto_izdaje"] > 2100)
            $validator['v_leto'] = "Nepravilen vnos";

        // ** polje za število knjig (omejitev na 300 izvodov)
        if ($request->AddBook["st_knjig"] == '')
            $validator['v_stevilo'] = "Polje je obvezno";
        else if (is_numeric($request->AddBook["st_knjig"]) == false)
            $validator['v_stevilo'] = "Polje mora vsebovati samo številke";
        else if ((int)$request->AddBook["st_knjig"] < 1 || (int)$request->AddBook["st_knjig"] > 300)
            $validator['v_stevilo'] = "Številka mora biti v rangu 1-300";

        // ** polje za "ident" - > identifikacijska številka knjige
        if ($request->AddBook["ident"] == '')
            $validator['v_ident'] = "Polje je obvezno";
        else if (is_numeric($request->AddBook["ident"]) == false)
            $validator['v_ident'] = "Polje mora vsebovati samo številke";
        else if (strlen($request->AddBook["ident"]) != 7)
            $validator['v_ident'] = "Ident. št. mora biti dolžine 7 številk";
        // **

        // ** ali "ident" že obstaja ?  (identifikacija)
        $ident = Book::orderBy('naslov', 'ASC')
            ->select(['book.ident'])
            ->where('book.ident', '=', $request->AddBook["ident"])
            ->get();

        if (count($ident) > 0 && strlen($request->AddBook["ident"]) == 7) {
            $validator['v_ident'] = "Identifikacijska št. že obstaja";
        }


        // ** Preveri, če izbrana slika dejansko obstaja v mapi kjer so slike naslovnic knjig. V primeru, da je karkoli narobe, izbere default sliko, ki je "blank.jpg"
        $setPicName = "blank.jpg";
        $filesName = File::files(public_path('\bookImages'));
        foreach ($filesName as $fileName) {
            if ($fileName->getFilename() == $request->AddBook["picName"]) {
                $setPicName = $request->AddBook["picName"];
            }
        }

        // ** V primeru, da ima kateri izmed validatorjev sporočilo oz. je polje neveljavno, potem se shranjevanje ne izvede, na frontend se pošljejo validatorji za polja
        foreach ($validator as $valid) {
            if ($valid != false)
                $validOK = false;
        }

        // ** Če so polja pravilno izpolnjena, potem se izvede shranjevanje
        if ($validOK == true) {

            $books = new Book;

            for ($x = 1; $x <= $request->AddBook["st_knjig"]; $x++) {
                $books = new Book;
                $books->naslov = $request->AddBook["naslov"];
                $books->leto_izdaje = $request->AddBook["leto_izdaje"];
                $books->avtor = $request->AddBook["avtor"];
                $books->kategorija = $request->AddBook["kategorija"];
                $books->ident = $request->AddBook["ident"];
                $books->bookImage = $setPicName;
                $books->zap = $x;
                $books->save();
            }
            return $books;
        }
        // ** Vračanje validatorjev
        else {
            $returnJsn = json_encode($validator);
            return $returnJsn;
        }
    }

    // ** Izvede se update podatkov o knjigi
    public function update(Request $request, $id)
    {
        $selectedIDs = Book::query()
            ->select(['book.id'])
            ->where('book.ident', '=', $id)
            ->get();

        foreach ($selectedIDs as $item) {
            $existingItem = Book::find($item->id);
            if ($existingItem) {
                $existingItem->naslov = $request->lb_naslov;
                $existingItem->avtor = $request->lb_avtor;
                $existingItem->leto_izdaje = $request->lb_leto;
                $existingItem->kategorija = $request->lb_kategorija;
                $existingItem->updated_at = Carbon::now();
                $existingItem->save();
            }
        }

        return null;
    }

    // ** Izbris knjige
    public function destroy($ident_, $zap_)
    {
        rentBook::query()
            ->where('rentbook.id_ident', '=', $ident_)
            ->where('rentbook.id_zap', '=', $zap_)
            ->delete();

        Book::query()
            ->where('book.ident', '=', $ident_)
            ->where('book.zap', '=', $zap_)
            ->delete();
    }

    public function create()
    {
    }
}
