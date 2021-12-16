<template>
    <div>
        <div class="table-container">
            <table class="table table-condensed table-striped slider" style="margin-bottom: 0px" id="accordion">
                <thead id="idheadtable">
                    <tr>
                        <th style="width: 25%; vertical-align: middle">Naslov</th>
                        <th style="width: 20%; vertical-align: middle">Izvod</th>
                        <th style="width: 10%; vertical-align: middle">Datum od</th>

                        <th style="width: 15%; vertical-align: middle">Datum do</th>

                        <th style="width: 10%; vertical-align: middle">Status</th>
                        <th style="width: 10%; vertical-align: middle; text-align: center">
                            Zamuda&nbsp;&nbsp;<input type="checkbox" class="form-check-input" v-model="checkBoxToggle" true-value="1" false-value="0" @click="activeSearchingResRen('checkBox')" />
                        </th>

                        <th style="width: 10%; vertical-align: middle; text-align: center">
                            <button class="btn btn-primary" @click="showSearchResRen()">
                                <div>&nbsp;<font-awesome-icon icon="search" />&nbsp; <font-awesome-icon icon="hammer" /></div>
                            </button>
                        </th>
                    </tr>
                    <tr id="idShowSearchResRen" style="display: none">
                        <td style="width: 25%">
                            <input style="width: 100%" type="text" v-model="resRen_s_01" v-on:input="activeSearchingResRen(null)" placeholder="Išči naslov" />
                        </td>
                        <td style="width: 20%"></td>
                        <td style="width: 10%"></td>

                        <td style="width: 15%"></td>

                        <td style="width: 25%" colspan="3">
                            <div style="text-align: right">
                                Višina tabele:
                                <button class="btn btn-primary" style="display: inline-block" @click="resizeTableResRen('-')">
                                    <div><font-awesome-icon icon="minus" /></div>
                                </button>
                                <div style="display: inline-block">{{ this.heightTableResRen }}</div>
                                <button class="btn btn-primary" style="display: inline-block" @click="resizeTableResRen('+')">
                                    <div><font-awesome-icon icon="plus" /></div>
                                </button>
                            </div>
                        </td>
                    </tr>
                </thead>
                <tbody id="tableResRen">
                    <tr v-for="(resRen, index) in this.copyAllResRen" :key="index">
                        <td colspan="7" style="margin: 0px; padding: 0px">
                            <table style="width: 100%">
                                <tr>
                                    <td style="width: 25%; border-top: 0px; vertical-align: middle">{{ resRen.naslov }}</td>

                                    <td style="width: 20%; border-top: 0px; vertical-align: middle">{{ resRen.id_ident }} / {{ resRen.id_zap }}</td>

                                    <td style="width: 10%; border-top: 0px; vertical-align: middle">
                                        {{ resRen.datum_od }}
                                    </td>

                                    <td style="width: 15%; border-top: 0px; vertical-align: middle">
                                        {{ resRen.datum_do }}
                                    </td>

                                    <td style="width: 10%; border-top: 0px; vertical-align: middle">
                                        <div v-if="resRen.status == 0" title="Prosto" style="cursor: help"><div style="background-color: green; width: 75%; border-radius: 20px">&nbsp;</div></div>
                                        <div v-if="resRen.status == 1" title="Rezervirano" style="cursor: help">
                                            <div style="background-color: orange; width: 75%; border-radius: 20px">&nbsp;</div>
                                        </div>
                                        <div v-if="resRen.status == 2" title="Rezervirano in izposojeno" style="cursor: help">
                                            <div style="background-color: darkred; width: 75%; border-radius: 20px">&nbsp;</div>
                                        </div>
                                        <div v-if="resRen.status == 3" title="Izposojeno" style="cursor: help">
                                            <div style="background-color: tomato; width: 75%; border-radius: 20px">&nbsp;</div>
                                        </div>
                                    </td>
                                    <td v-if="resRen.prekoracitev == 1" style="width: 10%; border-top: 0px; vertical-align: middle; text-align: center; color: #e60000">
                                        <font-awesome-icon icon="times" />
                                    </td>
                                    <td v-if="resRen.prekoracitev == 0" style="width: 10%; border-top: 0px; vertical-align: middle; text-align: center"></td>
                                    <td style="width: 10%; border-top: 0px; text-align: center">
                                        <button class="btn btn-primary" data-toggle="collapse" v-bind:data-target="'#c_resRen' + resRen.id">
                                            <div><font-awesome-icon icon="pencil-alt" /></div>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="hiddenRow" style="padding-top: 0px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; border-top: 0px">
                                        <div v-bind:id="'c_resRen' + resRen.id" class="collapse" data-parent="#accordion">
                                            <!-- tabela za prikazovanje urejanja -->
                                            <table style="width: 100%">
                                                <tr colspan="6">
                                                    <td style="width: 25%; border-top: 0px" colspan="2">
                                                        <button
                                                            v-if="(resRen.status == '1' || resRen.status == '2') && resRen.prekoracitev != 1"
                                                            class="btn btn-primary"
                                                            @click="fnResRen(resRen.id, '01', null)"
                                                        >
                                                            <div>Končaj rezervacijo &nbsp; &nbsp;<font-awesome-icon icon="check-circle" /></div>
                                                        </button>
                                                        <button v-if="resRen.status == '2' || resRen.status == '3'" class="btn btn-primary" @click="fnResRen(resRen.id, '02', resRen.status)">
                                                            <div>Končaj izposojo &nbsp; &nbsp;<font-awesome-icon icon="check-circle" /></div>
                                                        </button>
                                                        <button v-if="resRen.status == '1' && resRen.p_renting == '1'" class="btn btn-primary" @click="fnResRen(resRen.id, '03', null)">
                                                            <div>Izposodi &nbsp; &nbsp;<font-awesome-icon icon="book-reader" /></div>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <button class="btn btn-outline-secondary btn_allBooksOpenClose" style="width: 100%" @click="openCloseTabResRen()">
                        <div><font-awesome-icon icon="arrows-alt-v" /></div>
                    </button>
                </tfoot>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    components: {},

    props: ['auth_user'],

    data: function () {
        return {
            heightTableResRen: '',
            tableResRen_closed: false,
            allResRen: [],
            copyAllResRen: [],
            resRen_s_01: '',
            resRen_s_02: '',
            checkBoxToggle: 0
        };
    },
    methods: {
        // ** Pridobi knjige, ki so v izposoji / rezervaciji določenega uporabnika
        getResRen() {
            axios
                .get('/resRent/resRents')
                .then((response) => {
                    this.allResRen = response.data;
                    this.copyAllResRen = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        // ** Prehod stanj med rezervacijo in izposojo
        fnResRen(idResRenTab_, resrent_, status_) {
            axios
                .put('/resRent/update/' + idResRenTab_, {
                    resRent: resrent_,
                    status: status_
                })
                .then((response) => {
                    if (response.status == 200) {
                        $('.collapse').collapse('hide');
                        this.ident = '';
                        this.getResRen();
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        // ** prikaže možnosti filtriranja ter beleženje višine tabele
        showSearchResRen() {
            if (this.heightTableResRen == '') {
                this.heightTableResRen = $('#tableResRen').css('max-height');
            }
            if ($('#idShowSearchResRen').css('display') == 'none') {
                $('#idShowSearchResRen').show();
            } else $('#idShowSearchResRen').hide();
        },

        // ** Filtriranje rezultatov glede na vnesene nize
        activeSearchingResRen($typeFilter) {
            this.copyAllResRen = this.allResRen;

            if ($typeFilter == 'checkBox' && this.checkBoxToggle == 0) this.checkBoxToggle = 1;
            else if ($typeFilter == 'checkBox' && this.checkBoxToggle == 1) this.checkBoxToggle = 0;

            // ** Če je označena prekoračitev oz zamuda, potem se to upošteva v filtriranju
            if (this.checkBoxToggle == 1) {
                this.copyAllResRen = this.copyAllResRen.filter((book) => {
                    return book.naslov.toLowerCase().match(this.resRen_s_01.toLowerCase()) && book.prekoracitev == this.checkBoxToggle;
                });
            } else {
                this.copyAllResRen = this.copyAllResRen.filter((book) => {
                    return book.naslov.toLowerCase().match(this.resRen_s_01.toLowerCase());
                });
            }
        },

        // ** Nastavljanje velikosti maximalne razširitve tabele
        resizeTableResRen($method_) {
            if ($method_ == '+') {
                var increase = parseInt(this.heightTableResRen) + 100;
                $('#tableResRen').css('max-height', increase);
                this.tableResRen_closed = false;
            } else if ($method_ == '-') {
                var decrease = parseInt(this.heightTableResRen) - 100;
                $('#tableResRen').css('max-height', decrease);
                this.tableResRen_closed = false;
            }
            this.heightTableResRen = $('#tableResRen').css('max-height');
        },

        // ** Zapiranje oz. odpiranje tabele
        openCloseTabResRen() {
            if (this.tableResRen_closed == true) {
                $('#tableResRen').css({ maxHeight: this.heightTableResRen });
                this.tableResRen_closed = false;
            } else {
                if (this.heightTableResRen == '') {
                    this.heightTableResRen = $('#tableResRen').css('max-height');
                }
                $('#tableResRen').css({ maxHeight: 0 });
                this.tableResRen_closed = true;
            }
        }
    },

    created() {
        this.getResRen();
    },

    // ** Filtriranje knjig glede na iskalne nize
    computed: {
        filteredResRen: function () {
            return this.allResRen.filter((book) => {
                return book.naslov.toLowerCase().match(this.resRen_s_01.toLowerCase()) && book.name.toLowerCase().match(this.resRen_s_02.toLowerCase());
            });
        }
    }
};
</script>
<style scoped>
.heading {
    background: #e6e6e6;
    padding: 10px;
}

#title {
    text-align: center;
}

.disabled {
    pointer-events: none;
    background-color: gray;
    border-color: gray;
}

.table-container {
    border: 1px solid gray;
    border-radius: 5px;
}

.slider {
    display: flex;
    flex-flow: column;
    height: 100%;
    width: 100%;
}
.slider thead {
    flex: 0 0 auto;
    width: calc(100% - 1em);
}
.slider tbody {
    max-height: 500px;
    display: block;
    overflow-y: scroll;
}
.slider tbody tr {
    width: 100%;
}
.slider thead,
.slider tbody tr {
    display: table;
    table-layout: fixed;
}
.btn_allBooksOpenClose {
    background-color: rgb(133 186 211) !important;
}
</style>
