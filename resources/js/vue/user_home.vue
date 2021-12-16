<template>
    <div>
        <div class="row">
            <div class="col-md-2">
                <div>
                    <h3>Filter</h3>
                </div>
                <br />
                <div>Naslov:</div>
                <div>
                    <input style="width: 100%" type="text" v-model="f_title" placeholder="Išči po naslovu" />
                </div>
                <br />
                <div>Avtor:</div>
                <div>
                    <input style="width: 100%" type="text" v-model="f_author" placeholder="Išči po avtorju" />
                </div>
                <br />
                <div>Leto:</div>
                <div>
                    <input style="width: 100%" type="text" v-model="f_year" placeholder="Išči po letu" />
                </div>
                <br />
                <div>Kategorija:</div>
                <div>
                    <input style="width: 100%" type="text" v-model="f_category" placeholder="Išči po kategoriji" />
                </div>
                <br />
                <div v-if="auth_user != undefined">
                    <div style="font-size: 1.1em; text-align: center">Datum / mesec izbora</div>

                    <div style="text-align: center">
                        <span id="id_dateDisplay" style="font-size: 1.2em"> {{ dateNowP.dateDisplay }} </span> <br />
                    </div>
                    <div></div>
                    <div style="text-align: center">
                        <button id="btnBackDate" :class="this.btn_back_date_d == true ? 'btn btn-secondary btn-sm disabled' : 'btn btn-primary btn-sm'" @click="setDate('back')">
                            <div><font-awesome-icon icon="backward" /></div>
                        </button>

                        <button class="btn btn-primary btn-sm" @click="setDate('now')">
                            <div><font-awesome-icon icon="angle-down" /></div>
                        </button>

                        <button class="btn btn-primary btn-sm" @click="setDate('next')">
                            <div><font-awesome-icon icon="forward" /></div>
                        </button>
                    </div>
                    <br /><br />
                    <div style="font-size: 1.1em; text-align: center">Obdobje dni</div>

                    <div style="text-align: center">
                        <span style="font-size: 1.2em"> {{ this.tempDurResRen }} </span> <br />
                    </div>
                    <div style="text-align: center">
                        <button id="btnBackDuration" :class="this.btn_back_duration_d == true ? 'btn btn-secondary disabled' : 'btn btn-primary'" @click="setDuration('back')">
                            <div><font-awesome-icon icon="backward" /></div>
                        </button>

                        <button id="btnForwDuration" :class="this.btn_next_duration_d == true ? 'btn btn-secondary disabled' : 'btn btn-primary'" @click="setDuration('next')">
                            <div><font-awesome-icon icon="forward" /></div>
                        </button>
                    </div>
                </div>
                <br /><br />
                <button :class="this.btn_searchChanged == false ? 'btn btn-primary container-fluid' : 'btn btn-warning container-fluid'" @click="filterBooks(true)">
                    <div><font-awesome-icon icon="search" /></div>
                </button>
            </div>

            <div class="col-md-10">
                <div>
                    <h2>Knjige</h2>
                </div>
                <div class="row">
                    <div v-for="(book, index) in this.allBooks" :key="index" class="col-md-3">
                        <div class="bookItemView">
                            <div>
                                <img style="width: 100%" :src="'/bookImages/' + book.bookImage" />
                            </div>
                            <div v-if="auth_user != undefined" class="row" style="margin: 0px">
                                <button
                                    type="button"
                                    @click="modal_resBook(index, book.ident, book.naslov, book.bookImage, 'showModal')"
                                    :class="book.p_reservation == false ? 'btn btn-primary col-md-6 bookItemBtn disabled' : 'btn btn-primary col-md-6 bookItemBtn'"
                                >
                                    Rezerviraj
                                </button>

                                <button
                                    type="button"
                                    @click="modal_rentBook(index, book.ident, book.rec_rent_ident, book.rec_rent_zap, book.naslov, book.bookImage)"
                                    :class="book.p_renting == false ? 'btn btn-success col-md-6 bookItemBtn disabled' : 'btn btn-success col-md-6 bookItemBtn'"
                                >
                                    Izposodi
                                </button>
                            </div>
                            <div v-if="auth_user == undefined" class="row" style="margin: 0px">
                                <a href="/login" type="button" class="btn btn-warning col-md-12 bookItemBtnLogin"><b>Login</b></a>
                            </div>
                            <div>
                                <h4>{{ book.naslov }}</h4>
                            </div>
                            <div>
                                <h5>Avtor - {{ book.avtor }}</h5>
                            </div>
                            <div>
                                <h5>Leto izdaje - {{ book.leto_izdaje }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="allBooks.length" v-observe-visibility="handleScrolledToBottom"></div>
        <!-- modalno okno -->
        <div id="modalBlur" v-if="modalEnabled == true">
            <div id="modalInfo" :class="this.modalMode == 'res' ? 'modalInfo' : 'modalInfo modalMaxRen'" v-click-outside="(e) => closeModal(e)">
                <div>
                    <div>
                        <button id="closeModal" type="button" @click="closeModal()" style="border: 0px; font-size: 1.6em; right: 10px; position: absolute; top: 10px">&times;</button>
                    </div>
                    <div style="border-bottom: 1px solid #d9d9d9; text-align: center">
                        <h4>{{ this.modalTitle }}</h4>
                    </div>

                    <div style="padding-top: 20px; margin: auto; width: 30%; text-align: center">
                        <img style="width: 100%" :src="'/bookImages/' + this.modalPicName" />
                    </div>
                    <div style="padding-top: 10px" class="padMar">
                        <div v-if="this.modalMode == 'res'">
                            <div>
                                {{ this.dateNowP.dateMonthName }}
                            </div>

                            <div>
                                <div v-for="(day, index2) in allBooks[this.reloadIndex].p_resBook" :key="index2" style="padding: 0px; display: inline-block">
                                    <div
                                        v-if="day == 'R'"
                                        @click="modal_resBook(index2 + 1, null, null, null, 'checkReservation')"
                                        :style="
                                            modalResConfirm == false
                                                ? 'background-color: #00b359; text-align: center; cursor: pointer; width: 30px'
                                                : 'background-color: #00b359; text-align: center; cursor: default; pointer-events: none; width:30px'
                                        "
                                    >
                                        {{ index2 + 1 }}
                                    </div>
                                    <div v-if="day == 'P'" style="background-color: gray; text-align: center; cursor: default; width: 30px">{{ index2 + 1 }}</div>
                                </div>
                            </div>
                        </div>
                        <div v-if="this.modalMode == 'res'" class="row padMar">
                            <div class="col-md-6" style="text-align: left">
                                <form>
                                    <div class="form-group row" style="margin-bottom: 0px">
                                        <h5 style="text-align: right; padding-right: 0px">Naslov:</h5>
                                        <h5 class="col-md-9">{{ this.AddResRent.naslov }}</h5>
                                    </div>
                                    <div class="form-group row">
                                        <h5 style="text-align: right; padding-right: 0px">Izvod:</h5>
                                        <h5 class="col-md-9">{{ this.AddResRent.id_ident }} / {{ this.AddResRent.id_zap }}</h5>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6" style="text-align: left">
                                <form>
                                    <div class="form-group row" style="margin-bottom: 0px">
                                        <h5 class="" style="text-align: right; padding-right: 0px">Datum od:</h5>
                                        <h5 class="col-md-8">{{ this.AddResRent.datum_od }}</h5>
                                    </div>
                                    <div class="form-group row">
                                        <h5 class="" style="text-align: right; padding-right: 0px">Datum do:</h5>
                                        <h5 class="col-md-8">{{ this.AddResRent.datum_do }}</h5>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div v-if="this.modalMode == 'ren'" class="row padMar">
                            <div class="col-md-12" style="text-align: left">
                                <form>
                                    <div class="form-group row" style="margin-bottom: 0px">
                                        <h5 class="col-md-6" style="text-align: right; padding-right: 0px">Naslov:</h5>
                                        <h5 class="col-md-6">{{ this.AddResRent.naslov }}</h5>
                                    </div>
                                    <div class="form-group row" style="margin-bottom: 0px">
                                        <h5 class="col-md-6" style="text-align: right; padding-right: 0px">Izvod:</h5>
                                        <h5 class="col-md-6">{{ this.AddResRent.id_ident }} / {{ this.AddResRent.id_zap }}</h5>
                                    </div>
                                    <div class="form-group row" style="margin-bottom: 0px">
                                        <h5 class="col-md-6" style="text-align: right; padding-right: 0px">Datum od:</h5>
                                        <h5 class="col-md-6">{{ this.AddResRent.datum_od }}</h5>
                                    </div>
                                    <div class="form-group row">
                                        <h5 class="col-md-6" style="text-align: right; padding-right: 0px">Datum do:</h5>
                                        <h5 class="col-md-6">{{ this.AddResRent.datum_do }}</h5>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div v-if="this.modalMode == 'res'" class="d-flex justify-content-center">
                            <button
                                id="btnBackDuration"
                                :class="this.btn_reservation_d == true ? 'btn btn-secondary disabled' : 'btn btn-primary'"
                                @click="modal_resBook(null, null, null, null, 'confirmReservation')"
                            >
                                Potrdi rezervacijo
                            </button>
                        </div>
                    </div>
                    <div v-if="this.modalInfoPass == true" style="margin-top: 20px; text-align: center; color: green; font-size: 1.5em">Uspešno izvedeno</div>
                </div>
            </div>
        </div>
        <!-- konec modalnega okna -->
    </div>
</template>

<script>
export default {
    components: {},

    props: ['auth_user'],

    data: function () {
        return {
            allBooks: [],
            termDate: '',
            tempTermDate: '',
            durResRen: 10,
            tempDurResRen: 10,
            btn_back_date_d: true,
            btn_back_duration_d: true,
            btn_next_duration_d: false,
            btn_searchChanged: false,

            scrolledToBottom: false,

            reloadIdent: '',
            reloadIndex: '',
            modalEnabled: false,
            modalMode: '',
            modalTitle: '',
            modalPicName: '',
            modalResConfirm: false,
            modalInfoPass: true,
            btn_reservation_d: true,

            f_title: null,
            f_author: null,
            f_year: null,
            f_category: null,

            AddResRent: {
                id_ident: '',
                id_zap: '',
                id_member: '',
                datum_od: '',
                datum_do: '',
                status: '',
                naslov: ''
            },

            dateNowP: [
                {
                    dateDisplay: '',
                    dateDefault: '',
                    dateFirstDay: '',
                    dateMonthName: '',
                    tempMonthName: ''
                }
            ]
        };
    },
    methods: {
        // ? '/{od}/{do}/{date}/{duration}/{title}/{author}/{year}/{category}/{mode}/{ident}'
        // ** Nalaganje knjig za prikaz uporabniku, glede na izbrane filtre. Knjige se nalagajo sproti, ko uporabnik pride do konca strani
        getAllBooks($count, $loadNumber, $date_, $duration_, $title_, $author_, $year_, $category_) {
            axios
                .get('/resRent/userBooks/' + $count + '/' + $loadNumber + '/' + $date_ + '/' + $duration_ + '/' + $title_ + '/' + $author_ + '/' + $year_ + '/' + $category_ + '/list' + '/' + null)
                .then((response) => {
                    if (this.allBooks.length > 0) {
                        // ** Dopolnjevanje seznama knjig, ki so že prikazane s tistimi, ki se naložijo ob koncu strani
                        this.allBooks = [...this.allBooks, ...response.data];
                    } else {
                        this.allBooks = response.data;
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        // ** nastavitev meseca izposoje
        setDate($method) {
            var dateNow = new Date();
            var months = ['Januar', 'Februar', 'Marec', 'April', 'Maj', 'Junij', 'Julij', 'Avgust', 'September', 'Oktober', 'November', 'December'];

            if ($method == 'now') {
                this.tempTermDate = this.dateNowP.dateDefault;
                $('#id_dateDisplay').text(this.dateNowP.dateDisplay);
                this.btn_back_date_d = true;
                this.dateNowP.tempMonthName = months[dateNow.getMonth()];
            } else if ($method == 'next') {
                var dateCurrent = new Date(this.tempTermDate == '' || this.btn_back_date_d == true ? this.dateNowP.dateFirstDay : this.tempTermDate);
                var dateNext = new Date(dateCurrent.getFullYear(), dateCurrent.getMonth() + 1, dateCurrent.getDate());
                this.tempTermDate = dateNext.getFullYear() + '-' + ('0' + (dateNext.getMonth() + 1)).slice(-2) + '-' + ('0' + dateNext.getDate()).slice(-2);
                $('#id_dateDisplay').text(months[dateNext.getMonth()] + ', ' + dateNext.getFullYear());
                this.btn_back_date_d = false;
                this.dateNowP.tempMonthName = months[dateNext.getMonth()];
            } else if ($method == 'back') {
                var dateCurrent = new Date(this.tempTermDate);
                var dateBack = new Date(dateCurrent.getFullYear(), dateCurrent.getMonth() - 1, dateCurrent.getDate());
                $('#id_dateDisplay').text(months[dateBack.getMonth()] + ', ' + dateBack.getFullYear());

                if (dateBack.getFullYear() == dateNow.getFullYear() && dateBack.getMonth() + 1 == dateNow.getMonth() + 1) {
                    this.btn_back_date_d = true;
                    $('#id_dateDisplay').text(('0' + dateNow.getDate()).slice(-2) + '.' + ('0' + (dateNow.getMonth() + 1)).slice(-2) + '.' + dateNow.getFullYear());
                    dateBack = dateNow;
                }

                this.tempTermDate = dateBack.getFullYear() + '-' + ('0' + (dateBack.getMonth() + 1)).slice(-2) + '-' + ('0' + dateBack.getDate()).slice(-2);
                this.dateNowP.tempMonthName = months[dateBack.getMonth()];
            }
            this.checkSearchBtn();
        },

        // ** nastavitev obdobja izposoje
        setDuration($method) {
            if ($method == 'back') {
                this.tempDurResRen = this.tempDurResRen - 10;
                if (this.tempDurResRen == '10') {
                    this.btn_back_duration_d = true;
                } else {
                    this.btn_back_duration_d = false;
                    this.btn_next_duration_d = false;
                }
            } else if ($method == 'next') {
                this.tempDurResRen = this.tempDurResRen + 10;
                if (this.tempDurResRen == '30') {
                    this.btn_next_duration_d = true;
                } else {
                    this.btn_back_duration_d = false;
                    this.btn_next_duration_d = false;
                }
            }
            this.checkSearchBtn();
        },

        // ** Modalno okno za rezervacijo knjige
        modal_resBook: function (index_, ident_, naslov_, picName_, mode_) {
            // ** Če modalno okno še ni prikazano
            if (mode_ == 'showModal') {
                this.modalMode = 'res';
                this.modalTitle = 'Rezervacija';
                this.modalEnabled = true;
                this.AddResRent.naslov = naslov_;
                this.AddResRent.id_ident = ident_;
                this.reloadIdent = ident_;
                this.reloadIndex = index_;
                this.modalInfoPass = false;
                this.modalPicName = picName_;
            }
            // ** Če je modalno okno že odprto
            else if (mode_ == 'checkReservation') {
                var dateFrom = new Date(this.termDate);
                dateFrom.setDate(index_);
                var dateTo = new Date(this.termDate);
                dateTo.setDate(index_);
                dateTo.setDate(dateTo.getDate() + this.durResRen - 1);

                (this.AddResRent.id_member = this.auth_user.id),
                    (this.AddResRent.status = 1), // status 1 = rezervacija
                    (this.AddResRent.datum_od = ('0' + dateFrom.getDate()).slice(-2) + '.' + ('0' + (dateFrom.getMonth() + 1)).slice(-2) + '.' + dateFrom.getFullYear()),
                    (this.AddResRent.datum_do = ('0' + dateTo.getDate()).slice(-2) + '.' + ('0' + (dateTo.getMonth() + 1)).slice(-2) + '.' + dateTo.getFullYear());

                // ** vrne kateri izvod je primeren za rezervacijo (izbere najbolj zasedenega, za čimboljši izkoristek rezervacij)
                axios
                    .get('/resRent/findResBook/' + this.AddResRent.id_ident + '/' + this.termDate + '/' + this.durResRen + '/' + index_)
                    .then((response) => {
                        this.AddResRent.id_zap = response.data;
                        this.btn_reservation_d = false;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            }
            // ** Izvede se rezervacija
            else if (mode_ == 'confirmReservation') {
                axios
                    .post('/resRent/store', {
                        AddResRent: this.AddResRent
                    })
                    .then((response) => {
                        if (response.status == 201) {
                            this.modalResConfirm = true;
                            this.modalInfoPass = true;
                            this.btn_reservation_d = true;
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            }
        },

        // ** Izvede izposojo knjige
        modal_rentBook: function (index_, ident_, rec_rent_ident_, rec_rent_zap_, naslov_, picName_) {
            var dateFrom = new Date();

            var dateTo = new Date(dateFrom);
            dateTo.setDate(dateTo.getDate() + this.durResRen - 1);

            (this.AddResRent.id_ident = rec_rent_ident_),
                (this.AddResRent.id_zap = rec_rent_zap_),
                (this.AddResRent.id_member = this.auth_user.id),
                (this.AddResRent.status = 3), // status 3 = izposoja
                (this.AddResRent.datum_od = ('0' + dateFrom.getDate()).slice(-2) + '.' + ('0' + (dateFrom.getMonth() + 1)).slice(-2) + '.' + dateFrom.getFullYear()),
                (this.AddResRent.datum_do = ('0' + dateTo.getDate()).slice(-2) + '.' + ('0' + (dateTo.getMonth() + 1)).slice(-2) + '.' + dateTo.getFullYear());
            this.AddResRent.naslov = naslov_;

            this.reloadIdent = ident_;
            this.reloadIndex = index_;
            axios
                .post('/resRent/store', {
                    AddResRent: this.AddResRent
                })
                .then((response) => {
                    if (response.status == 201) {
                        this.modalMode = 'ren';
                        this.modalTitle = 'Izposoja';
                        this.modalEnabled = true;
                        this.modalPicName = picName_;
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        // ? '/{od}/{do}/{date}/{duration}/{title}/{author}/{year}/{category}/{mode}/{ident}'
        // ** Ko zapremo modalno okno se nastavitve ponastavijo, naloži pa tudi sveže podatke o knjigi, možnosti izposoje, rezervacije in terminih rezervacije, če je rezervacija v podanem obdobju mogoča
        closeModal() {
            this.modalEnabled = false;

            this.AddResRent.id_ident = '';
            this.AddResRent.id_zap = '';
            this.AddResRent.id_member = '';
            this.AddResRent.datum_od = '';
            this.AddResRent.datum_do = '';
            this.AddResRent.status = '';
            this.AddResRent.naslov = '';
            this.modalPicName = '';

            this.modalResConfirm = false;
            this.modalInfoPass = true;

            axios
                .get('/resRent/userBooks/' + null + '/' + null + '/' + this.termDate + '/' + this.durResRen + '/' + null + '/' + null + '/' + null + '/' + null + '/book' + '/' + this.reloadIdent)
                .then((response) => {
                    // ** Posodobitev informacij določene knjige na prikaznem seznamu, po tem ko se izvede rezervacija / izposoja
                    this.allBooks[this.reloadIndex].p_renting = response.data[0].p_renting;
                    this.allBooks[this.reloadIndex].p_resBook = response.data[0].p_resBook;
                    this.allBooks[this.reloadIndex].p_reservation = response.data[0].p_reservation;
                    this.allBooks[this.reloadIndex].rec_rent_ident = response.data[0].rec_rent_ident;
                    this.allBooks[this.reloadIndex].rec_rent_zap = response.data[0].rec_rent_zap;

                    this.reloadIdent = '';
                    this.reloadIndex = '';
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        // ** Filtriranje knjig glede na iskalne nize (boolFresh -> Seznam knjig se izprazni in se naloži novi, če pritisnemo iskanje knjige. Funkcija se uporablja tudi pri samodejnem nalaganju knjig ko pridemo na konec strani)
        filterBooks($boolFresh) {
            if ($boolFresh == true) {
                this.allBooks = [];
                this.dateNowP.dateMonthName = this.dateNowP.tempMonthName;
                this.termDate = this.tempTermDate;
                this.durResRen = this.tempDurResRen;
                this.btn_searchChanged = false;
            }

            this.f_title == '' ? (this.f_title = null) : (this.f_title = this.f_title);
            this.f_author == '' ? (this.f_author = null) : (this.f_author = this.f_author);
            this.f_year == '' ? (this.f_year = null) : (this.f_year = this.f_year);
            this.f_category == '' ? (this.f_category = null) : (this.f_category = this.f_category);
            this.getAllBooks(this.allBooks.length, 8, this.termDate, this.durResRen, this.f_title, this.f_author, this.f_year, this.f_category);
        },

        // ** Preveri, če so bile spremembe pri nastavitvi termina oz. obdobja izposoja / rezervacije
        checkSearchBtn() {
            if (this.termDate != this.tempTermDate || this.durResRen != this.tempDurResRen) {
                this.btn_searchChanged = true;
            } else {
                this.btn_searchChanged = false;
            }
        },

        // ** funkcija, ki sproži dodatno nalaganje knjig, ko pridemo do konca strani
        handleScrolledToBottom(isVisible) {
            if (!isVisible) {
                return;
            }

            this.filterBooks();
        }
    },

    created() {
        let month = ['Januar', 'Februar', 'Marec', 'April', 'Maj', 'Junij', 'Julij', 'Avgust', 'September', 'Oktober', 'November', 'December'];

        var dateNow = new Date();
        this.dateNowP.dateDisplay = ('0' + dateNow.getDate()).slice(-2) + '.' + ('0' + (dateNow.getMonth() + 1)).slice(-2) + '.' + dateNow.getFullYear();
        this.dateNowP.dateDefault = dateNow.getFullYear() + '-' + ('0' + (dateNow.getMonth() + 1)).slice(-2) + '-' + ('0' + dateNow.getDate()).slice(-2);
        this.dateNowP.dateFirstDay = dateNow.getFullYear() + '-' + ('0' + (dateNow.getMonth() + 1)).slice(-2) + '-01';
        this.dateNowP.dateMonthName = month[dateNow.getMonth()];
        this.dateNowP.tempMonthName = month[dateNow.getMonth()];
        this.termDate = this.dateNowP.dateDefault;
        this.tempTermDate = this.dateNowP.dateDefault;

        // ? '/{od}/{do}/{date}/{duration}/{title}/{author}/{year}/{category}'
        this.getAllBooks(0, 8, this.termDate, this.durResRen, null, null, null, null);
    },

    computed: {}
};
</script>
<style scoped>
#title {
    text-align: center;
}

.bookItemView {
    margin-top: 20px;
}

.disabled {
    pointer-events: none;
    background-color: gray;
    border-color: gray;
}

.bookItemBtn {
    border-radius: 0px 0px 5px 5px;
    width: 100%;
    display: inline-block;
    padding: 0px 2px 0px 2px;
}

.bookItemBtnLogin {
    border-radius: 0px 0px 5px 5px;
    width: 100%;
}

#modalBlur {
    background-color: rgba(88, 88, 88, 0.555);
    backdrop-filter: blur(2px);
    position: fixed;
    margin: auto;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
.modalInfo {
    background-color: rgb(255, 255, 255);
    border-radius: 10px;
    box-shadow: 5px 3px 5px #474747;
    padding: 10px;
    position: sticky;
    margin: auto;
    top: 10%;
    right: 0;
    bottom: 0;
    left: 0;
    width: auto;
    height: auto;
    max-width: 600px;
}

.modalMaxRen {
    max-width: 400px;
}
#closeModal {
    background-color: transparent;
    color: rgb(180, 180, 180);
}

#closeModal:hover {
    color: rgb(0, 0, 0);
}

#modalTable tr td {
    font-size: 1em;
    padding-left: 8px;
}

#modalTable tr {
    padding-top: 4px;
}

#modalTable tr td:first-child {
    text-align: right;
}

.padMar {
    padding-left: 0px;
    padding-right: 0px;
    margin-left: 0px;
    margin-right: 0px;
}
</style>
