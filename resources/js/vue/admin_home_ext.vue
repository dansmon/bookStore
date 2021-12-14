<template>
    <div>
        <div class="table-container">
            <table class="table table-condensed table-striped" style="margin-bottom: 0px" id="accordion">
                <thead id="idheadtable">
                    <tr>
                        <th style="width: 25%; vertical-align: middle">Naslov</th>
                        <th style="width: 20%; vertical-align: middle">Avtor</th>
                        <th style="width: 10%; vertical-align: middle">Leto izdaje</th>

                        <th style="width: 15%; vertical-align: middle">Kategorija</th>

                        <th style="width: 10%; vertical-align: middle">St knjig</th>
                        <th style="width: 15%; vertical-align: middle" colspan="2">
                            <button class="btn btn-primary" @click="showSearch()">
                                <div>&nbsp;<font-awesome-icon icon="search" />&nbsp; <font-awesome-icon icon="hammer" /></div>
                            </button>
                            <button class="btn btn-primary" @click="showAddForm()">
                                <div>&nbsp; &nbsp;<font-awesome-icon icon="plus" />&nbsp; &nbsp;</div>
                            </button>
                        </th>
                    </tr>
                    <tr id="idShowSearch" style="display: none">
                        <td style="width: 25%">
                            <input style="width: 100%" type="text" v-model="t_tab_01" v-on:input="activeSearching()" placeholder="Išči naslov" />
                        </td>
                        <td style="width: 20%">
                            <input style="width: 100%" type="text" v-model="t_tab_02" v-on:input="activeSearching()" placeholder="Išči avtorja" />
                        </td>
                        <td style="width: 10%">
                            <input style="width: 100%" type="text" v-model="t_tab_03" v-on:input="activeSearching()" placeholder="Išči leto" />
                        </td>

                        <td style="width: 15%">
                            <input style="width: 100%" type="text" v-model="t_tab_04" v-on:input="activeSearching()" placeholder="Išči kategorijo" />
                        </td>
                        <td style="width: 25%" colspan="3">
                            <div style="text-align: right">
                                Višina tabele:
                                <button class="btn btn-primary" style="display: inline-block" @click="resizeTable('-')">
                                    <div><font-awesome-icon icon="minus" /></div>
                                </button>
                                <div style="display: inline-block">{{ this.heightTable }}px</div>
                                <button class="btn btn-primary" style="display: inline-block" @click="resizeTable('+')">
                                    <div><font-awesome-icon icon="plus" /></div>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr id="idAdd0" class="addForm" style="display: none">
                        <td style="width: 25%" colspan="7">
                            <h3>Dodaj knjigo</h3>
                        </td>
                    </tr>

                    <tr id="idAdd1" class="addForm" style="display: none">
                        <td style="width: 25%; border-top: 0px">
                            <input style="width: 100%" type="text" :class="v_naslov != false ? 'form-control is-invalid' : 'form-control'" v-model="AddBook.naslov" placeholder="Naslov knjige" />
                            <span v-if="v_naslov != false" class="validatorText">{{ this.v_naslov }}</span>
                        </td>
                        <td style="width: 20%; border-top: 0px">
                            <input style="width: 100%" type="text" :class="v_avtor != false ? 'form-control is-invalid' : 'form-control'" v-model="AddBook.avtor" placeholder="Naslov Avtorja" />
                            <span v-if="v_avtor != false" class="validatorText">{{ this.v_avtor }}</span>
                        </td>
                        <td style="width: 10%; border-top: 0px">
                            <input style="width: 100%" type="text" :class="v_leto != false ? 'form-control is-invalid' : 'form-control'" v-model="AddBook.leto_izdaje" placeholder="Leto" />
                            <span v-if="v_leto != false" class="validatorText">{{ this.v_leto }}</span>
                        </td>

                        <td style="width: 15%; border-top: 0px">
                            <input style="width: 100%" type="text" :class="v_kategorija != false ? 'form-control is-invalid' : 'form-control'" v-model="AddBook.kategorija" placeholder="Kategorija" />
                            <span v-if="v_kategorija != false" class="validatorText">{{ this.v_kategorija }}</span>
                        </td>

                        <td style="width: 10%; border-top: 0px">
                            <input style="width: 100%" type="text" :class="v_stevilo != false ? 'form-control is-invalid' : 'form-control'" v-model="AddBook.st_knjig" placeholder="stevilo" />
                            <span v-if="v_stevilo != false" class="validatorText">{{ this.v_stevilo }}</span>
                        </td>

                        <td style="width: 5%; border-top: 0px"></td>
                        <td style="width: 10%; border-top: 0px"></td>
                    </tr>

                    <tr id="idAdd2" class="addForm" style="display: none">
                        <td style="width: 25%; border-top: 0px; vertical-align: middle">
                            <input style="width: 100%" type="text" :class="v_ident != false ? 'form-control is-invalid' : 'form-control'" v-model="AddBook.ident" placeholder="Identifikacija" />
                            <span v-if="v_ident != false" class="validatorText">{{ this.v_ident }}</span>
                        </td>
                        <td @mouseover="mouseOver($event)" @mouseleave="mouseLeave($event)" style="width: 20%; border-top: 0px">
                            <model-list-select :list="bookPicLookup" v-model="picSelected" option-value="namePic" :custom-text="codeAndName" placeholder="Izberi sliko"> </model-list-select>
                        </td>
                        <td style="width: 10%; border-top: 0px">
                            <button class="btn btn-primary" @click="addItem()">
                                <div>Shrani &nbsp;<font-awesome-icon icon="save" /></div>
                            </button>
                        </td>
                    </tr>
                </thead>
                <tbody id="tableAllBooks">
                    <tr v-for="(book, index) in allBooks" :key="index">
                        <td colspan="7" style="margin: 0px; padding: 0px">
                            <table style="width: 100%">
                                <tr>
                                    <td style="width: 25%; border-top: 0px">
                                        {{ book.naslov }}
                                    </td>

                                    <td style="width: 20%; border-top: 0px">
                                        {{ book.avtor }}
                                    </td>

                                    <td style="width: 10%; border-top: 0px">
                                        {{ book.leto_izdaje }}
                                    </td>

                                    <td style="width: 15%; border-top: 0px">
                                        {{ book.kategorija }}
                                    </td>

                                    <td style="width: 10%; border-top: 0px">
                                        {{ book.st_knjig }}
                                    </td>
                                    <td style="width: 5%; border-top: 0px">
                                        <button class="btn btn-primary" data-toggle="collapse" v-bind:data-target="'#sh_edit' + book.ident">
                                            <div><font-awesome-icon icon="pencil-alt" /></div>
                                        </button>
                                    </td>
                                    <td style="width: 10%; border-top: 0px">
                                        <button class="btn btn-primary" @click="reloadSubBooks(book.ident)" data-toggle="collapse" v-bind:data-target="'#subBooks' + book.ident">
                                            <div><font-awesome-icon icon="trash" /></div>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="hiddenRow" style="padding-top: 0px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; border-top: 0px">
                                        <div v-bind:id="'sh_edit' + book.ident" class="collapse" data-parent="#accordion">
                                            <!-- tabela za prikazovanje urejanja -->
                                            <table style="width: 100%">
                                                <tr>
                                                    <td style="width: 25%; border-top: 0px">
                                                        <input class="form-control" type="text" v-model="idU_naslov" v-bind:id="'idU_naslov' + book.ident" v-bind:placeholder="book.naslov" />
                                                    </td>

                                                    <td style="width: 20%; border-top: 0px">
                                                        <input class="form-control" type="text" v-model="idU_avtor" v-bind:id="'idU_avtor' + book.ident" v-bind:placeholder="book.avtor" />
                                                    </td>

                                                    <td style="width: 10%; border-top: 0px">
                                                        <input
                                                            class="form-control"
                                                            type="text"
                                                            v-model="idU_letoIzdaje"
                                                            v-bind:id="'idU_letoIzdaje' + book.ident"
                                                            v-bind:placeholder="book.leto_izdaje"
                                                        />
                                                    </td>

                                                    <td style="width: 15%; border-top: 0px">
                                                        <input
                                                            class="form-control"
                                                            type="text"
                                                            v-model="idU_kategorija"
                                                            v-bind:id="'idU_kategorija' + book.ident"
                                                            v-bind:placeholder="book.kategorija"
                                                        />
                                                    </td>

                                                    <td style="width: 25%; border-top: 0px" colspan="2">
                                                        <button class="btn btn-primary" @click="updateBook(book)">
                                                            <div>Shrani &nbsp; &nbsp;<font-awesome-icon icon="save" /></div>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div v-bind:id="'subBooks' + book.ident" class="collapse" data-parent="#accordion">
                                            <!-- tabela za prikazovanje urejanja -->

                                            <table style="width: 100%">
                                                <tr>
                                                    <table id="tab_subBooks" class="table-light">
                                                        <thead style="background-color: #acc9e3; border-radius: 10px 10px 0px 0px">
                                                            <tr style="border-radius: 10px 10px 0px 0px">
                                                                <td style="width: 35%; padding-left: 5px">Naslov</td>
                                                                <td style="width: 15%">Izvod</td>
                                                                <td style="width: 20%; text-align: center">V rezervaciji</td>
                                                                <td style="width: 20%; text-align: center">V izposoji</td>
                                                                <td style="width: 10%"></td>
                                                                <td style="width: 1em"></td>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="table-secondary">
                                                            <tr v-for="(subBooks, index2) in subBooks" :key="index2">
                                                                <td style="border-top: 0px; width: 35%">
                                                                    <span style="margin-left: 3%">{{ subBooks.naslov }}</span>
                                                                </td>
                                                                <td style="border-top: 0px; width: 15%">{{ subBooks.ident }} / {{ subBooks.zap }}</td>
                                                                <td v-if="subBooks.status_rezervacija == false" style="border-top: 0px; width: 20%; text-align: center; color: #2eb82e">
                                                                    <font-awesome-icon icon="times" />
                                                                </td>
                                                                <td v-if="subBooks.status_rezervacija == true" style="border-top: 0px; width: 20%; text-align: center; color: #e60000">
                                                                    <font-awesome-icon icon="check" />
                                                                </td>
                                                                <td v-if="subBooks.status_izposoja == false" style="border-top: 0px; width: 20%; text-align: center; color: #2eb82e">
                                                                    <font-awesome-icon icon="times" />
                                                                </td>
                                                                <td v-if="subBooks.status_izposoja == true" style="border-top: 0px; width: 20%; text-align: center; color: #e60000">
                                                                    <font-awesome-icon icon="check" />
                                                                </td>
                                                                <td style="border-top: 0px; width: 10%">
                                                                    <button
                                                                        :class="subBooks.btn_color ? 'btn btn-danger' : 'btn btn-primary'"
                                                                        style="margin-left: 3%"
                                                                        @click="removeBook(subBooks.ident, subBooks.zap, subBooks.btn_color)"
                                                                    >
                                                                        <div><font-awesome-icon icon="trash" /></div>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="thumbnail" v-if="this.thumbnail == true">
            <img v-if="this.picSelected != ''" style="width: 100%" :src="'/bookImages/' + this.picSelected" />
        </div>
        <div id="modalBlur" v-if="modalEnabled == true">
            <div id="modalInfo" v-click-outside="(e) => closeModal()">
                <div>
                    <div>
                        <button id="closeModal" type="button" @click="closeModal()" style="border: 0px; font-size: 1.6em; right: 10px; position: absolute; top: 10px">&times;</button>
                    </div>
                    <div style="border-bottom: 1px solid #d9d9d9; text-align: center">
                        <h4>{{ this.modalTitle }}</h4>
                    </div>

                    <div v-if="this.modalMode == 'warning'" style="padding-top: 20px; padding-left: 12px; padding-right: 12px">
                        <p style="color: red; font-size: 1.3em">
                            Pozor! <br /><br />Izbrisali boste knjigo katera ima beležene aktivne rezervacije oz. je v izposoji. <br />V primeru izbrisa se izbrišejo tudi rezervacije in izposoje
                        </p>
                    </div>

                    <div v-if="this.modalMode == 'success'" style="padding-top: 80px; padding-left: 12px; padding-right: 12px; text-align: center">
                        <p style="color: green; font-size: 1.3em">Knjiga je bila uspešno dodana v knjižno zbirko.</p>
                    </div>
                    <div style="position: absolute; right: 10px; bottom: 10px">
                        <button v-if="this.modalMode == 'warning'" class="btn btn-danger" @click="removeBook(null, null, false)">
                            <div><font-awesome-icon icon="trash" /> Izbriši</div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ModelListSelect } from 'vue-search-select';
export default {
    props: ['allBooks', 'subBooks'],

    data: function () {
        return {
            ident: '',
            zap: '',
            t_tab_01: '',
            t_tab_02: '',
            t_tab_03: '',
            t_tab_04: '',
            bookPicLookup: [],
            picSelected: 'blank.jpg',
            thumbnail: false,
            formValidation: true,

            x: 0,
            y: 0,
            viewportHeight: 1,
            viewportWidth: 1,

            heightTable: '',
            idU_naslov: '',
            idU_avtor: '',
            idU_letoIzdaje: '',
            idU_kategorija: '',

            modalEnabled: false,
            modalMode: '',
            modalTitle: '',

            v_naslov: false,
            v_avtor: false,
            v_leto: false,
            v_kategorija: false,
            v_stevilo: false,
            v_ident: false,

            AddBook: {
                naslov: '',
                leto_izdaje: '',
                avtor: '',
                kategorija: '',
                ident: '',
                st_knjig: '',
                picName: ''
            }
        };
    },

    created() {
        this.updateViewportSize();
    },

    mounted() {
        document.addEventListener('mousemove', this.onMouseMove);
        window.addEventListener('resize', this.updateViewportSize);
    },

    unmounted() {
        document.removeEventListener('mousemove', this.onMouseMove);
        window.removeEventListener('resize', this.updateViewportSize);
    },

    computed: {
        styles() {
            return {
                left: percent(this.x, this.viewportWidth),
                top: percent(this.y, this.viewportHeight)
            };

            function percent(value, total) {
                return Math.round((value * 100) / total) + '%';
            }
        }
    },

    methods: {
        // ** Ko premikamo miško se nastavlja X in Y, da se slika prikazuje ob miški (malo ima tudi offset-a)
        onMouseMove(ev) {
            if (this.picSelected == '') this.thumbnail = false;
            if (this.thumbnail == true) {
                this.x = ev.clientX + 10;
                this.y = ev.clientY + 10;
                $('#thumbnail').css({ top: this.y, left: this.x });
            }
        },

        // ** Nastavi velikost dejanskega prikaza strani
        updateViewportSize() {
            this.viewportHeight = window.innerHeight;
            this.viewportWidth = window.innerWidth;
        },

        // ** Pridobi vsa imena slik, ki se nahajajo v public/bookImages
        getPicName() {
            axios
                .get('/book/bookPic')
                .then((response) => {
                    this.bookPicLookup = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        // ** Prikaz imena slike
        codeAndName(item) {
            return `${item.namePic}`;
        },

        selectOption2() {
            this.picSelected = this.bookPicLookup[0].namePic;
        },

        // ** prikaže div, ki vsebuje sliko, ko gremo z miško čez spustni meni
        mouseOver: function (event) {
            if (this.picSelected == '') this.thumbnail = false;
            else this.thumbnail = true;
        },

        // ** Skrije div, ki vsebuje sliko, po tem ko miška zapusti mesto kjer je vnosno okno
        mouseLeave: function (event) {
            this.thumbnail = false;
        },

        // ** Nastavi višino tabele, prikaže iskalna polja za filtriranje knjig
        showSearch() {
            this.heightTable = $('#tableAllBooks').css('max-height').replace('px', '');
            if ($('#idShowSearch').css('display') == 'none') {
                $('#idShowSearch').show();
            } else $('#idShowSearch').hide();
        },

        // ** funkcija, ki omogoča pošiljanje podatkov v nadrejeno .vue, kjer se filtrirajo podatki v polju, glede na iskalne vnose
        activeSearching: function () {
            var tabSearch = {
                tab1: this.t_tab_01,
                tab2: this.t_tab_02,
                tab3: this.t_tab_03,
                tab4: this.t_tab_04
            };

            this.$emit('searchChanges', tabSearch);
        },

        // ** Prikaže vnosna polja za dodajanje knjige
        showAddForm() {
            this.getPicName();
            if ($('.addForm').css('display') == 'none') {
                $('.addForm').show();
                $('#idheadtable').css({ 'margin-bottom': '20px' });
            } else {
                $('.addForm').hide();
                $('#idheadtable').css({ 'margin-bottom': '0px' });
            }
        },

        // ** Funkcija za dodajanje knjige - dodani validatorji, ki se izvedejo na backendu in prikažejo na frontendu
        addItem() {
            this.AddBook.picName = this.picSelected;

            axios
                .post('/book/store', {
                    AddBook: this.AddBook
                })
                .then((response) => {
                    if (response.status == 200) {
                        // ** Če validiranje ni uspelo, potem pokaže napako vnosa za določeno polje
                        this.v_naslov = response.data.v_naslov;
                        this.v_avtor = response.data.v_avtor;
                        this.v_kategorija = response.data.v_kategorija;
                        this.v_leto = response.data.v_leto;
                        this.v_stevilo = response.data.v_stevilo;
                        this.v_ident = response.data.v_ident;
                    }
                    // ** Če je knjiga uspešno dodana in je uspešno prestala validiranje - > ponastavijo se nastavitve ter prikaže se modalno okno
                    if (response.status == 201) {
                        this.AddBook.naslov = '';
                        this.AddBook.leto_izdaje = '';
                        this.AddBook.avtor = '';
                        this.AddBook.kategorija = '';
                        this.AddBook.ident = '';
                        this.AddBook.st_knjig = '';
                        this.AddBook.picName = '';
                        this.picSelected = 'blank.jpg';

                        this.v_naslov = false;
                        this.v_avtor = false;
                        this.v_leto = false;
                        this.v_kategorija = false;
                        this.v_stevilo = false;
                        this.v_ident = false;

                        this.modalEnabled = true;
                        this.modalMode = 'success';
                        this.modalTitle = 'Dodajanje knjige';

                        this.$emit('reloadAllBooks');
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        // ** Ponovno naloži podknjige oz izvode določene knjige, v primeru, da kliknemo knjigo z drugim identifikatorjem
        reloadSubBooks(ident_) {
            if (this.ident != ident_) {
                this.ident = ident_;
                this.$emit('reloadSubBooks', ident_);
            } else {
                this.ident = '';
            }
        },

        // ** Na knjigi možna sprememba naslova, avtorja, leto izdaje, kategorija
        updateBook(idUpdate) {
            axios
                .put('/book/update/' + idUpdate.ident, {
                    lb_naslov: this.idU_naslov == '' ? idUpdate.naslov : this.idU_naslov,
                    lb_avtor: this.idU_avtor == '' ? idUpdate.avtor : this.idU_avtor,
                    lb_leto: this.idU_letoIzdaje == '' ? idUpdate.leto_izdaje : this.idU_letoIzdaje,
                    lb_kategorija: this.idU_kategorija == '' ? idUpdate.kategorija : this.idU_kategorija
                })
                .then((response) => {
                    if (response.status == 200) {
                        this.$emit('reloadAllBooks');
                        idU_naslov = '';
                        idU_avtor = '';
                        idU_letoIzdaje = '';
                        idU_kategorija = '';
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        // ** Brisanje knjige
        removeBook(ident_, zap_, warningBool) {
            // ** Če ima knjiga beleženo rezervacijo oz. izposojo, potem je kar tako ne moremo izbrisati. Najprej se pokaže obvestilo o tem, da se bodo izbrisale vse rezervacije in izposoje, če nadaljujemo brisanje
            if (warningBool == false) {
                if (this.modalEnabled == false) {
                    this.ident = ident_;
                    this.zap = zap_;
                }

                // ** Izvedemo izbris
                axios
                    .delete('/book/remove/' + this.ident + '/' + this.zap)
                    .then((response) => {
                        if (response.status == 200) {
                            this.$emit('reloadSubBooks', this.ident);

                            if (this.subBooks.length == 1) {
                                $('.collapse').collapse('hide');
                                this.$emit('reloadAllBooks');
                            }
                            this.modalEnabled = false;
                            this.ident = '';
                            this.zap = '';
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            }
            // ** Prikaže se obvestilo
            else {
                this.modalEnabled = true;
                this.modalMode = 'warning';
                this.modalTitle = 'Brisanje knjige';
                this.ident = ident_;
                this.zap = zap_;
            }
        },

        // ** zapre modalno okno
        closeModal() {
            this.modalEnabled = false;
            this.modalMode = '';
            this.modalTitle = '';
        },
        // ** Povečanje / zmanjšanje maksimalne velikosti tabele
        resizeTable($method_) {
            if ($method_ == '+') {
                var increase = parseInt(this.heightTable) + 100;
                $('#tableAllBooks').css('max-height', increase);
            } else if ($method_ == '-') {
                var decrease = parseInt(this.heightTable) - 100;
                $('#tableAllBooks').css('max-height', decrease);
            }
            this.heightTable = $('#tableAllBooks').css('max-height').replace('px', '');
        }
    },
    components: {
        ModelListSelect
    }
};
</script>

<style scoped>
.item {
    background: #e6e6e6;
    padding: 5px;
    margin-top: 5px;
}

.row {
    height: 300px;
    overflow-y: scroll;
}

.table-container {
    border: 1px solid gray;
    border-radius: 5px;
}
table {
    display: flex;
    flex-flow: column;
    height: 100%;
    width: 100%;
}
table thead {
    flex: 0 0 auto;
    width: calc(100% - 1em);
}
table tbody {
    max-height: 300px;
    display: block;
    overflow-y: scroll;
}
table tbody tr {
    width: 100%;
}
table thead,
table tbody tr {
    display: table;
    table-layout: fixed;
}
#tab_subBooks {
    margin-left: 5%;
    margin-bottom: 2%;
    margin-top: 2%;
    border-radius: 10px;
    width: 90%;
}

#tab_subBooks td {
    border-top: 0px;
    border-radius: 10px;
    vertical-align: middle;
}

#tab_subBooks thead {
    width: calc(100%);
}

#tab_subBooks tbody {
    max-height: 200px;
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
#modalInfo {
    background-color: rgb(255, 255, 255);
    border-radius: 10px;
    box-shadow: 5px 3px 5px #474747;
    padding: 10px;
    position: absolute;
    margin: auto;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    width: 670px;
    height: 300px;
}

#closeModal {
    background-color: transparent;
    color: rgb(180, 180, 180);
}

#closeModal:hover {
    color: rgb(0, 0, 0);
}

#thumbnail {
    background-color: rgb(255, 255, 255);
    border-radius: 5px;
    box-shadow: 5px 3px 5px #474747;

    position: fixed;
    width: 141px;
    height: 200px;
}

.validatorText {
    color: red;
}

.validatorInput {
    background-color: #ffd2d2;
    border: solid #ff8080;
    border-radius: 2px;
}
</style>
