<template>
    <div class="resRenContainer">
        <admin-resren-ext
            :allResRen="filteredResRen"
            :allBooks="filteredAllBooks"
            :subBooks="subBooks"
            :dateNowP="dateNowP"
            v-on:changeReservation="updateReservation($event)"
            v-on:reloadResRen="getResRen()"
            v-on:reloadAllBooks="getAllBooks()"
            v-on:reloadSubBooks="getSubBooks($event)"
            v-on:searchChangesResRen="searchFilterResRen($event)"
            v-on:searchChangesAllBooks="searchFilterAllBooks($event)"
        />
    </div>
</template>

<script>
import AdminResrenExt from './admin_resren_ext.vue';

export default {
    components: {
        AdminResrenExt
    },
    data: function () {
        return {
            allResRen: [],
            allBooks: [],
            subBooks: [],
            dateNowP: [
                {
                    dateDisplay: '',
                    dateDefault: '',
                    dateFirstDay: ''
                }
            ],
            searchNaslovResRen: '',
            searchOsebaResRen: '',
            searchIzvodResRen: '',
            checkBoxResRen: 0,

            searchNaslovAllBooks: '',
            searchAvtorAllBooks: '',
            searchLetoAllBooks: '',
            searchKategAllBooks: ''
        };
    },
    methods: {
        // ** Pridobi knjige, ki imajo beleženo aktivno rezervacijo oz izposojo
        getResRen() {
            axios
                .get('/resRent/resRents')
                .then((response) => {
                    this.allResRen = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        // ** Pridobi knjige, ki so v knjižnici
        getAllBooks() {
            axios
                .get('/book/books')
                .then((response) => {
                    this.allBooks = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        // ** Pridobi izvode knjig in informacije o prostih terminih glede na to, za katero obdobje smo želeli knjigo rezervirati ter za kako dolgo
        getSubBooks: function (subBooksFields) {
            this.subBooks = [];

            axios
                .get('/resRent/subBooks/' + subBooksFields.id_ident + '/' + subBooksFields.period + '/' + subBooksFields.duration)
                .then((response) => {
                    this.subBooks = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        // ** nastavi datum od - do za rezervacijo
        updateReservation: function (updateFields) {
            var dateFrom = new Date(updateFields.period);
            dateFrom.setDate(updateFields.day);
            var dateTo = new Date(updateFields.period);
            dateTo.setDate(updateFields.day);
            dateTo.setDate(dateTo.getDate() + updateFields.duration - 1);

            for (let item of this.subBooks) {
                if (item.ident == updateFields.id_ident && item.zap == updateFields.id_zap) {
                    item.r_datumOD = ('0' + dateFrom.getDate()).slice(-2) + '.' + ('0' + (dateFrom.getMonth() + 1)).slice(-2) + '.' + dateFrom.getFullYear();
                    item.r_datumDO = ('0' + dateTo.getDate()).slice(-2) + '.' + ('0' + (dateTo.getMonth() + 1)).slice(-2) + '.' + dateTo.getFullYear();
                }
            }
        },

        // ** Filtriranje iskanja v tabeli rezervacije / izposoje
        searchFilterResRen: function (updateFields) {
            this.searchNaslovResRen = updateFields.tab1;
            this.searchIzvodResRen = updateFields.tab2;
            this.searchOsebaResRen = updateFields.tab3;
            this.checkBoxResRen = updateFields.checkBox;
        },

        // ** Filtriranje iskanja v tabeli, ki prikazuje vse knjige v knjižnici
        searchFilterAllBooks: function (updateFields) {
            this.searchNaslovAllBooks = updateFields.tab1;
            this.searchAvtorAllBooks = updateFields.tab2;
            this.searchLetoAllBooks = updateFields.tab3;
            this.searchKategAllBooks = updateFields.tab4;
        }
    },

    created() {
        // ** Ob kreiranju pridobi podatke o knjigah, ki so v rezervaciji oz izposoji, ter vse knjige, ki obstajajo v knjižnici
        this.getResRen();
        this.getAllBooks();

        // ** nastavitev formata datuma za prikaz, za obdelavo in za nastavitev prvega dneva v mesecu
        var dateNow = new Date();
        this.dateNowP.dateDisplay = ('0' + dateNow.getDate()).slice(-2) + '.' + ('0' + (dateNow.getMonth() + 1)).slice(-2) + '.' + dateNow.getFullYear();
        this.dateNowP.dateDefault = dateNow.getFullYear() + '-' + ('0' + (dateNow.getMonth() + 1)).slice(-2) + '-' + ('0' + dateNow.getDate()).slice(-2);
        this.dateNowP.dateFirstDay = dateNow.getFullYear() + '-' + ('0' + (dateNow.getMonth() + 1)).slice(-2) + '-01';
    },

    computed: {
        // ** Filtriranje knjig v tabeli, ki prikazuje rezervacijo oz. izposojo
        filteredResRen: function () {
            // ** Če je označeno, da se prikažejo samo knjige, ki presegajo doboljen rok izposoje
            if (this.allResRen.length > 0) {
                if (this.checkBoxResRen == 1) {
                    return this.allResRen.filter((book) => {
                        return (
                            book.naslov.toLowerCase().match(this.searchNaslovResRen.toLowerCase()) &&
                            book.name.toLowerCase().match(this.searchOsebaResRen.toLowerCase()) &&
                            book.fn_izvod.toLowerCase().match(this.searchIzvodResRen.toLowerCase()) &&
                            book.prekoracitev == this.checkBoxResRen
                        );
                    });
                }
                // ** Ni omejitve pri prikazovanju preseganja roka izposoje
                else {
                    return this.allResRen.filter((book) => {
                        return (
                            book.naslov.toLowerCase().match(this.searchNaslovResRen.toLowerCase()) &&
                            book.name.toLowerCase().match(this.searchOsebaResRen.toLowerCase()) &&
                            book.fn_izvod.toLowerCase().match(this.searchIzvodResRen.toLowerCase())
                        );
                    });
                }
            }
        },

        // ** Filtriranje tabele pri prikazu vseh knjig, glede na iskalne nize
        filteredAllBooks: function () {
            return this.allBooks.filter((book) => {
                return (
                    book.naslov.toLowerCase().match(this.searchNaslovAllBooks.toLowerCase()) &&
                    book.avtor.toLowerCase().match(this.searchAvtorAllBooks.toLowerCase()) &&
                    book.leto_izdaje.match(this.searchLetoAllBooks) &&
                    book.kategorija.toLowerCase().match(this.searchKategAllBooks.toLowerCase())
                );
            });
        }
    }
};
</script>
<style scoped>
.resRenContainer {
    margin: auto;
}
.heading {
    background: #e6e6e6;
    padding: 10px;
}

#title {
    text-align: center;
}
</style>
