<h1 align="center">Book Store</h1><br />
Book Store je spletna knjižnica za rezervacijo in izposojo knjig. Za rezervacijo / izposojo knjige se je potrebno registrirati in prijaviti. Poznamo tri vrste dostopa:<br /><br />
<ul>
<li>Neprijavljenim uporabnikom se prikaže samo pogled nad knjigami, ki so v knjižnici.</li> 
<li>Navadnemu uporabniku sistem omogoča: </li>
    <ul>
        <li>rezervacijo / izposojo knjige</li>
        <li>pregled nad svojimi rezervacijami / izposojamim ter urejanje le teh</li>
    </ul>
<li>Administratorju / knjižničarju sistem omogoča:</li>
    <ul>
        <li>pregled nad vsemi knjigami</li>
        <li>dodajanje knjig</li>
        <li>urejanje knjig</li>
        <li>brisanje posameznih izvodov knjig</li>
        <li>pregled in urejanje rezervacij / izposoj vseh uporabnikov</li>
        <li>dodeljevanje rezervacij / izposoj uporabnikom</li>        
    </ul>
</ul>

<h3>Uporabljena ogrodja in tehnologije</h3>
<ul>
   <li>Laravel</li>
   <li>Vue</li>
   <li>Bootstrap</li>
   <li>PHP</li>
   <li>JavaScript</li>
   <li>MySQL</li>
   <li>XAMPP</li>
</ul>

***
<h2>1. Namestitev in zagon</h2>
<h3>1.1 Baza</h3>
V <b>phpMyAdmin</b> kreiramo bazo z imenom <b>dbbookstore</b>, saj je že v projektu vse pripravljeno za takojšnjo uporabo.<br /><br />
V terminalu je potrebno pognati ukaz <b>php artisan migrate:fresh –-seed</b> saj s tem kreiramo tabele v bazi podatkov, prav tako se vnese še administrator (<i>admin@gmail.com, geslo123</i>), kateri ima drugačen dostop kot uporabnik, ki se registrira preko obrazca registracije. <br /><br />
Za v namen testiranja in pregleda aplikacije lahko naložimo že kreirane knjige z insert ukazom v bazi <b>dbbookstore</b> v tabeli <b>book</b>. Insert ukaz najdemo v mapi našega projekta <u><i>bookStore/database/sampleBooks/sampleBooks.txt</i></u>

<h3>1.2 Terminal</h3>
Pred zagonom projekta je potrebno pognati še naslednje ukaze.<br />
<ul>
<li>npm i --save @fortawesome/fontawesome-svg-core</li>
<li>npm i --save @fortawesome/free-brands-svg-icons</li>
<li>npm i --save @fortawesome/free-regular-svg-icons</li>
<li>npm i --save @fortawesome/free-solid-svg-icons</li></li>
<li>npm i --save @fortawesome/vue-fontawesome</li>
<li>npm i vue-search-select</li>
</ul>
Poženemo še ukaza za zagon <br />
<ul>
<li>php artisan serve</li>
<li>npm run hot</li>
</ul>
<br /><br />

***
<p align="center"><b>Prikaz poteka izvedb posameznih akcij se sklada z datumom 17.12.2021.</b></p>

***

<br />
<h2>2. Javni pogled </h2>
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/01.png">
<ul>
<li>Brez potrebnega vpisa oz. registracije lahko vidimo pregled vseh knjig, ki se nahajajo v »bookStore«, omejene so pa funkcionalnosti rezervacije ter izposoje.</li>
<li>Funkcionalnost sprotnega nalaganja knjig ko s pogledom pridemo do konca strani.</li>
</ul>

<h2>3. Uporabniški pogled</h2>
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/02.png">
Če je v sistem prijavljen uporabnik z osnovnimi pravicami, potem se mu na domači strani pokaže seznam knjig kot je prikazan na sliki zgoraj z možnostjo rezervacije oz. izposoje za določeno časovno obdobje v določenem terminu, če je to mogoče. <br />
Če rezervacija oz. izposoja ni mogoča, potem ne moremo klikniti na gumb, ki je ponazorjen s sivo barvo.

<h3>3.1 Izposoja</h3>
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/03.png">
Sistem za posamezno knjigo preveri vse njene izvode, če je katerikoli izvod mogoč za izposojo na trenutni dan (nima aktivne rezervacije oz. tekoče izposoje), potem je gumb pod knjigo zelene barve in izposoja se lahko izvede. <br /><br />
Sistem samodejno določi, kateri izvod knjige bo uporabniku izposodil na podlagi zasedenosti knjig. Določi se izvod knjige, ki je še mogoč za izposojo in je ob enem najbolj zasedena knjiga izmed vseh izvodov knjig (da ostanejo ostali izvodi bolj prosti in razpoložljivi). <br /><br />
Po uspešni izposoji se uporabniku prikaže modalno okno s sporočilom o uspešni izvedbi ter informacijo o terminu izposoje ter izvodom knjige. Po zaprtju modalnega okna se posodobijo podatki o trenutni knjigi (mogoče rezervacije, izposoje).
    
<h3>3.2 Rezervacija</h3>
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/04.png">Glede na določen termin rezervacije in obdobje (10, 20 ali 30), sistem pri vseh izvodih knjige preveri od katerega datuma naprej je možno opraviti rezervacijo. Vse možne kombinacije rezervacij združi ter prikaže kot zelen kvadratek z datumom, ostali dnevi, kjer rezervacija ni mogoča so sive barve. <br /><br />

<br /><img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/05.png"><br />
Izbrali smo datum 17.12.2021. Preračuna se do kdaj bo knjiga rezervirana prav tako pa sistem določi kateri izvod določene knjige se bo rezerviral izmed vseh, ki so na voljo za rezervacijo (v tem primeru izvod 3, ki je mimogrede zaporedna številka knjige v bazi, z identifikacijsko številko 6100000).<br /><br />

<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/06.png"><br />
Primer prostih terminov v mesecu januarju.<br /><br />

<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/07.png"><br />
Ko se rezervacija uspešno zabeleži, dobimo sporočilo o uspešni izvedbi.
Po zaprtju modalnega okna se posodobijo podatki o trenutni knjigi (mogoče rezervacije, izposoje).

<h3>3.3 Urejanje rezervacij in izposoj</h3>
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/08.png">

<h4>3.3.1 Status</h4>
V tabeli so izbrane rezervacije oz. izposoje samo od določenega prijavljenega uporabnika. Rezervacije in izposoje so ponazorjene z določeno barvo v stolpcu »Status«. <br /><br />
<ul>
 <li><img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/09.png"> Rezervirano</li>
 <li><img  src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/10.png"> Izposojeno</li>
 <li><img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/11.png"> Rezervirano in izposojeno</li>
</ul>

<h4>3.3.2 Dodatne možnosti tabele</h4>
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/12.png">S klikom na gumb za lupo in kladivo se nam prikažejo možnosti povečave tabele (maksimalna višina) ter iskalnik po naslovu knjig. S puščico na dnu tabele lahko tabelo zapremo in rezultati, ki so prikazani v tabeli niso vidni.

<h4>3.3.3 Zamuda pri vrnitvi knjige</h4>
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/13.png">Z izborom »Zamuda« se prikažejo samo knjige, ki so trenutno v izposoji in še niso bile vrnjene do datuma »datum do«. 

<h4>3.3.4 Prehod med statusi</h4>

<h5>3.3.4.1 Končaj rezervacijo</h5>
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/14.png">Če je status »Rezervirano« in današnji dan (17.12.2021) še ni v okviru termina rezervacije, potem se prikaže samo gumb »Končaj rezervacijo«. 

<h5>3.3.4.2 Končaj rezervacijo. Izposoja knjige</h5>
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/15.png">Če je status »Rezervirano« in je današnji dan (17.12.2021) v okviru termina rezervacije, potem se prikaže gumb za izposojo knjige, kot tudi gumb, da lahko rezervacijo končamo.

<h5>3.3.4.3 Končaj rezervacijo ali končaj izposojo</h5>
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/16.png">Knjiga je po kliku na gumb izposodi v statusu »Rezervirano in izposojeno«. V tem primeru je ponujena možnost, da končamo izposojo in preide status nazaj v »Rezervirano« (knjigo smo vrnili, vendar jo imamo še vedno rezervirano in si jo lahko kasneje spet izposodimo). V primeru klika na »končaj rezervacijo«, knjiga preide v status »končano« in na seznamu ni več prikazana.

<h5>3.3.4.3 Končaj izposojo</h5>

<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/17.png"><br />Prikazan samo gumb »končaj izposojo«, saj knjiga ni bila rezervirana in ne more preiti v stanje rezervacije.<br /><br />

<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/18.png"><br />
Knjigam s prekoračitvijo oz. zamudo vrnitve se gumb »končaj rezervacijo« ne pokaže, pa čeprav so bile rezervirane in so prešle v status izposoje.

<h2>4. Administratorski pogled</h2> 
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/19.png">
Če je v sistem prijavljen administrator, se mu na domači strani pokaže tabela s seznamom knjig. Administrator lahko knjige ureja, briše ali dodaja. 
    
<h3>4.1 Možnosti tabele</h3>
Za dodatne možnosti tabele stisnemo na lupo in kladivo. Prikažejo se nam iskalna polja ter nastavitev višine tabele.
<h4>4.1.1 Iskalnik</h4>

<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/20.png"><br />
Iščemo lahko po naslovu, avtorju, letu izdaje ter kategoriji. Iskalnik deluje na principu "vsebuje", torej lahko iščemo po delčku besede, tako kot je prikazano na sliki zgoraj. 

<h4>4.1.2 Višina tabele </h4>
Na tabeli lahko nastavimo maksimalno velikost tabele, za boljši pregled nad podatki. 
<br />Primerjava:
<br /><br />Velikost 200 px:
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/21.png">

Velikost 500 px:<br />
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/22.png">

<h4>4.1.3 Dodajanje knjige</h4>
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/23.png">
Za dodajanje knjige stisnemo na gumb "+" v zgornjem desnem kotu. Prikaže se nam obrazec za dodajanje knjige

<h5>4.1.3.1 Validatorji</h5>
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/24.png">
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/25.png">
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/26.png">
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/27.png">
    
<h5>4.1.3.2 Obrazec za dodajanje knjige</h5>
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/28.png">Za dodano knjigo izberemo tudi naslovnico (slike naslovnic knjig dodamo v <i>bookstore/public/bookImages</i>), ki jo bo knjiga imela. Ko smo sliko izbrali, se nam kot predogled pokaže, če miško postavimo na spustni seznam. <br /><br />

<br /><img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/29.png"><br />
Ko je knjiga uspešno dodana se prikaže modalno okno s sporočilom. <br /><br />

<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/30.png"><br />
V tabeli že lahko vidimo vneseno knjigo če smo prijavljeni kot administrator. <br /><br />

<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/31.png"><br />
Če smo prijavljeni kot navadni uporabnik.
    
<h4>4.1.4 Urejanje knjige</h4>
 Uredimo lahko naslov, avtorja, leto izdaje in kategorijo. Spremembe se izvedejo na vseh izvodih te knjige, ki so shranjene v bazi podatkov.<br /><br />
Pred spremembo:
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/32.png">

Po spremembi:<br />
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/33.png">
    
<h4>4.1.5 Brisanje izvodov knjig</h4>
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/34.png">Prikaže se seznam vseh izvodov knjig. Prikaže se tudi informacija o tem ali ima knjiga aktivno rezervacijo ali izposojo v stolpcu "V rezervaciji" in "V izposoji". Če knjigo želimo vseeno izbrisati, se prikaže modalno okno z opozorilom, sicer pa se izbriše brez opozorila.<br /><br />
    
<br /><img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/35.png"><br />
Izbrisali smo knjigo izvod 2300000/1. Po izbrisu se izbriše tudi iz seznama rezervacije in izposoje. <br /><br />
     
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/36.png"><br />
Seznam izvodov knjig po izbrisu.
    
<h4>4.1.6 Urejanje rezervacij in izposoj</h4>
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/37.png">Podoben sistem kot pri uporabniku za urejanje rezervacij in izposoj (točka 3.3 Urejanje rezervacij in izposoj), le da ima ta tabela še prikaz uporabnikov, ki so izvedli rezervacijo / izposojo. Ima tudi dodatna polja za filtriranje rezultatov.
    
<h4>4.1.7 Dodeljevanje rezervacij in izposoj uporabnikom</h4>
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/38.png">Tabela v dodatnih možnostih ponuja iskalna polja po naslovu, avtorju, letu izdaje ter kategoriji, nastavljanja višine tabele, nastavljanje termina ter obdobja izposoje. Preden pa lahko sploh pridobimo podrobnosti za posamezno knjigo pa moramo izbrati uporabnika, kateremu želimo rezervirati oz. izposoditi knjigo. 

<h5>4.1.7.1 Izbira uporabnika</h5>
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/39.png">Uporabnika izberemo s spustnega seznama, ki je predstavljen z informacijo o imenu uporabnika ter njegovo emšo številko. Spustni seznam omogoča tudi iskanje osebe in sicer po imenu ali emšo številki.

<h5>4.1.7.2 Rezervacija </h5>

<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/40.png"><br />
Administrator / knjižničar, ki želi izposoditi ali rezervirati določeno knjigo najprej vidi seznam izvodov knjig ter hitro informacijo o tem kako je katera zasedena (koliko prostih dni ima na izbiro ter koliko terminov glede na določen termin, indikator zasedenosti, ki hitro vizualizira uporabniku informacijo o tem za katero knjigo naj se odloči. V stolpcu izposoja je predstavljena informacija o tem ali je možna izposoja določenega izvoda na današnji dan za določen termin in obdobje ali ne). <br /><br />

<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/41.png"><br />
Administrator / knjižničar najprej na zelenem kvadratku izbere dan od katerega dalje želi rezervacijo. Avtomatsko se preračuna do kdaj bo knjiga rezervirana. <br /><br />
    
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/42.png"><br />
Po rezervaciji knjige administrator / knjižničar dobi obvestilo o uspešni izvedbi ter terminom rezervacije  (datum od - do). <br /><br />
 
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/43.png"><br />
Ko je bila knjiga uspešno rezervirana, se je posodobil tudi seznam (ki je na isti strani) rezervacij in izposoj. Rezervacija se je dodala na konec seznama.
    
<h5>4.1.7.3 Izposoja </h5>

<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/44.png"><br />
Izposoja se izvede samodejno od današnjega dneva do preračunenaga dneva, ki je bil nastavljen pri obdobju (10, 20, 30 dni). <br /><br />

<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/45.png"><br />
Po izposoji knjige administrator / knjižničar dobi obvestilo o uspešni izvedbi ter terminom izposoje  (datum od - do). <br /><br />
    
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/46.png"><br />
Ko je bila knjiga uspešno izposojena, se je posodobil tudi seznam (ki je na isti strani) rezervacij in izposoj. Izposoja se je dodala na konec seznama. <br /><br />

<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/47.png"><br />
Končno stanje pri prikazovanju prostih terminov za knjigo "Čas čudežev".

<h2>5. Sistem rezervacije in izposoje</h2>

Za primer bomo vzeli knjigo "Modri otok", ki nima beležene še nobene rezervacije ali izposoje ter nastavili termin na današnji dan in čas rezervacije 10 dni. <br /><br />
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/48.png">

Iz vsake vrstice za posemezen izvod knjige lahko vidimo informacije za kateri izvod knjige gre, prost izbor, termini, zasedenost ter izposojo. 

Nastavljen termin je 17.12.2021 (če gre za trenutni mesec, potem prikaže datum trenutnega dneva, sicer pokaže ime meseca ter leto).
- Prost izbor (dnevi) nam prikaže koliko dni v mesecu je možnih za rezervacijo od vseh možnih (število dni v mesecu), upošteva se čas rezervacije. 
- Stolpec Termin nam prikazuje koliko terminov je še možnih glede na razpoložljivost izbora. Upošteva se tudi čas rezervacije. Število dni v mesecu se deli s časom rezervacije, tako dobimo število vseh terminov, ki bi bili mogoči, če bi bili prosti vsi dnevi v mesecu. 
- Zasedenost prikazujemo z indikatorjem barv. Odvisno je tudi od časa rezervacije, na podlagi česar je izračunan % zasedenost. (0 % - temno rdeč; do 30 % - temno oranžen; 30 - 60 % - svetlo oranžen; od 60 % - rumeno zelen; 100 % - zelen).
- Izposoja prikazuje informacijo o tem ali je knjiga na današnji dan prosta in možna za prevzem ali ne. Če je izposoja možna, se ob izposoji samodejno preračuna do kdaj bo knjiga izposojena od današnjega dneva glede na čas rezervacije, ki smo ga izbrali. 

<h3>5.1 Test 01 - izvod: 1900000 / 1</h3>
<h4>5.1.1 Izposoja (prej - potem) - obdobje 10 dni (izvod: 1900000 / 1)</h4>
Prej:

<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/49.png">
Sivi kvadratki v tem primeru predstavljajo že pretekle dneve. Indikator svetlo oranžne barve, ker je zasedenost terminov 50 %. <br /><br />

Potem:<br />
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/50.png"><br />
Rdeči kvadratki ponazorijo rezervirane dneve oz. dneve ko  je knjiga v izposoji. Indikator temno oranžne barve zaradi zasedenosti terminov 75 %. Izposoja ni več mogoča. 

<h4>5.1.2 Rezervacija  (prej - potem) - obdobje 10 dni (izvod: 1900000 / 1)</h4>

Na istem izvodu knjige naredimo še rezervacijo od 29.12.2021 dalje. Še prej pa preverimo kako je z zasedenostjo v mesecu januarju.<br /><br />
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/51.png">
Vsi dnevi so prosti. Indikator zelene barve, ker je zasedenost terminov 0 %. Prestavimo nazaj na decemberski termin ter rezervirajmo na 29.12.2021. <br /><br />

<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/52.png"><br />
Ko se je rezervacija opravila vidimo, da nam ni ostalo prostih dni na izbiro (čeprav 27. in 28. dan nimata beležene rezervacije oz. izposoje). Indikator temno rdeče barve, ker je zasedenost terminov 100 %.
Zopet poglejmo za mesec januar.<br /><br />

<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/53.png"><br />
Rezervacija beležena na 29.12.2021 se konča v januarju 7.1.2022. Indikator rumeno-zelene barve, ker je zasedenost terminov 25 %.

<h3>5.2 Test 02 - izvod: 1900000 / 2</h3>

Opravimo rezervacijo 09.01.2022, nato prestavimo termin na tekoči mesec (december).<br /><br />
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/54.png"><br />
<br />
<h5>Preverimo kaj se dogaja z rezerviranjem v mesecu decembru, za različno dolgo obdobje rezervacije (10, 20, 30 dni).</h5> 

<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/55.png"><br />
Če izberemo obdobje rezervacije 10 dni, potem lahko rezerviramo 2 termina v mesecu decembru, na voljo pa imamo 14 različnih dni na katere lahko rezervacijo izberemo. <br /><br />

<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/56.png"><br />
Če izberemo obdobje rezervacije 20 dni, potem lahko rezerviramo 1 termin v mesecu decembru, na voljo pa imamo 4 različne dni na katere lahko rezervacijo izberemo. <br /><br />

<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/57.png"><br />
Če izberemo obdobje rezervacije 30 dni, potem ne moremo rezervirati termina v mesecu decembru.
    
<h4>5.3 Prekoračitev termina - blokada rezervacije / izposoje</h4>

<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/58.png"><br />
Knjige, ki so v izposoji in niso bile vrnjene do datuma, imajo prekoračitev oz. zamudnino. Ker se za takšne knjige ne ve ali bodo sploh vrnjene oz. kdaj bodo, sistem blokira nadaljne rezervacije in izposoje. Na sliki zgoraj vidimo primer knjige "Gorila na luni" izvod "3600000/1", ki bi morala biti vrnjena do 12.12.2021.

<br />
<img src="https://github.com/dansmon/bookStore/blob/main/public/gitHubReadMe/59.png">Ko želimo opraviti rezervacijo na knjigi "Gorila na luni" izvod "3600000/1", vidimo samo beležene rezervacije / izposoje z rdečo barvo, preostali dnevi so sive barve, kar pomeni, da ni na voljo za izbor termina rezervacije / izposoje. Prikazano je tudi sporočilo poleg gumba "Rezerviraj", da rezervacija / izposoja ni mogoča, dokler knjiga ni vrnjena. 
   
<br />
<br />
<br />

***

<p align="right">Avtor: Danijel Šmon</p>

***

