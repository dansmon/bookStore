<template>
    <div class="adminHomeContainer">
        <admin-home-ext :allBooks="filterBooks" :subBooks="subBooks" v-on:searchChanges="searchFilter($event)" v-on:reloadAllBooks="getAllBooks()" v-on:reloadSubBooks="getSubBooks($event)" />
    </div>
</template>

<script>
import AdminHomeExt from './admin_home_ext.vue';

export default {
    components: {
        AdminHomeExt
    },
    data: function () {
        return {
            allBooks: [],
            subBooks: [],
            searchNaslov: '',
            searchAvtor: '',
            searchLeto: '',
            searchKat: ''
        };
    },

    methods: {
        // ** Pridobi knjige z določenim identifikatorjem
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

        // ** Pridobi izvode določene knjige
        getSubBooks: function (ident) {
            this.subBooks = [];
            axios
                .get('/book/subBooks/' + ident)
                .then((response) => {
                    this.subBooks = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        // ** Filtriranje knjig glede na iskalne nize pridobljene iz druge .vue datoteke
        searchFilter: function (updateFields) {
            this.searchNaslov = updateFields.tab1;
            this.searchAvtor = updateFields.tab2;
            this.searchLeto = updateFields.tab3;
            this.searchKat = updateFields.tab4;
        }
    },
    created() {
        this.getAllBooks();
    },

    computed: {
        // ** Filtriranje pri iskalnih nizih
        filterBooks: function () {
            return this.allBooks.filter((book) => {
                return (
                    book.naslov.toLowerCase().match(this.searchNaslov.toLowerCase()) &&
                    book.avtor.toLowerCase().match(this.searchAvtor.toLowerCase()) &&
                    book.leto_izdaje.toLowerCase().match(this.searchLeto.toLowerCase()) &&
                    book.kategorija.toLowerCase().match(this.searchKat.toLowerCase())
                );
            });
        }
    }
};
</script>
<style scoped>
.adminHomeContainer {
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
