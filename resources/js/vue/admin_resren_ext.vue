<template>
    <div>
        <div class="table-container">
            <table class="table table-condensed table-striped slider" style="margin-bottom: 0px" id="accordion">
                <thead id="idheadtable">
                    <tr>
                        <th style="width: 20%; vertical-align: middle">Naslov</th>
                        <th style="width: 15%; vertical-align: middle">Izvod</th>
                        <th style="width: 15%; vertical-align: middle">Oseba</th>
                        <th style="width: 10%; vertical-align: middle">Datum od</th>

                        <th style="width: 10%; vertical-align: middle">Datum do</th>

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
                        <td style="width: 20%">
                            <input style="width: 100%" type="text" v-model="resRen_s_01" v-on:input="activeSearchingResRen(null)" placeholder="Išči naslov" />
                        </td>
                        <td style="width: 15%">
                            <input style="width: 100%" type="text" v-model="resRen_s_02" v-on:input="activeSearchingResRen(null)" placeholder="Išči izvod" />
                        </td>
                        <td style="width: 15%">
                            <input style="width: 100%" type="text" v-model="resRen_s_03" v-on:input="activeSearchingResRen(null)" placeholder="Išči osebo" />
                        </td>
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
                    <tr v-for="(resRen, index) in allResRen" :key="index">
                        <td colspan="7" style="margin: 0px; padding: 0px">
                            <table style="width: 100%">
                                <tr>
                                    <td style="width: 20%; border-top: 0px; vertical-align: middle">{{ resRen.naslov }}</td>

                                    <td style="width: 15%; border-top: 0px; vertical-align: middle">{{ resRen.id_ident }} / {{ resRen.id_zap }}</td>

                                    <td style="width: 15%; border-top: 0px; vertical-align: middle">
                                        {{ resRen.name }}
                                    </td>

                                    <td style="width: 10%; border-top: 0px; vertical-align: middle">
                                        {{ resRen.datum_od }}
                                    </td>

                                    <td style="width: 10%; border-top: 0px; vertical-align: middle">
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
                                    <td v-if="resRen.prekoracitev == true" style="width: 10%; border-top: 0px; vertical-align: middle; text-align: center; color: #e60000">
                                        <font-awesome-icon icon="times" />
                                    </td>
                                    <td v-if="resRen.prekoracitev == false" style="width: 10%; border-top: 0px; vertical-align: middle; text-align: center"></td>
                                    <td style="width: 10%; border-top: 0px; text-align: center">
                                        <button class="btn btn-primary" data-toggle="collapse" v-bind:data-target="'#sh_resRen' + resRen.id">
                                            <div><font-awesome-icon icon="pencil-alt" /></div>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="hiddenRow" style="padding-top: 0px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; border-top: 0px">
                                        <div v-bind:id="'sh_resRen' + resRen.id" class="collapse" data-parent="#accordion">
                                            <!-- tabela za prikazovanje urejanja -->
                                            <table style="width: 100%">
                                                <tr colspan="6">
                                                    <td style="width: 25%; border-top: 0px" colspan="2">
                                                        <button v-if="resRen.status == '1' || resRen.status == '2'" class="btn btn-primary" @click="fnResRen(resRen.id, '01', null)">
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
        <div></div>
        <div style="margin-top: 50px">
            <div class="table-container">
                <table class="table table-condensed table-striped slider" style="margin-bottom: 0px" id="accordion2">
                    <thead id="idheadtable">
                        <tr>
                            <th style="width: 25%; vertical-align: middle">Naslov</th>
                            <th style="width: 20%; vertical-align: middle">Avtor</th>
                            <th style="width: 10%; vertical-align: middle">Leto izdaje</th>
                            <th style="width: 15%; vertical-align: middle">Kategorija</th>
                            <th style="width: 10%; vertical-align: middle">St knjig</th>
                            <th style="width: 15%; vertical-align: middle; text-align: center">
                                <button class="btn btn-primary" @click="showSearchAllBooks()">
                                    <div>&nbsp;<font-awesome-icon icon="search" />&nbsp;</div>
                                </button>
                                <button v-bind:id="'btn_Options'" class="btn btn-primary" @click="showOptions()">
                                    <div><font-awesome-icon icon="clock" />&nbsp; <font-awesome-icon icon="user" />&nbsp; <font-awesome-icon icon="hammer" /></div>
                                </button>
                            </th>
                        </tr>
                        <tr id="idShowOptions" style="display: none">
                            <td style="width: 100%">
                                <div>
                                    <div style="font-size: 1.1em; text-align: center">Datum / mesec izbora</div>

                                    <div style="text-align: center">
                                        <span id="id_dateDisplay" style="font-size: 1.2em"> {{ dateNowP.dateDisplay }} </span> <br />
                                    </div>
                                    <div style="text-align: center">
                                        <button id="btnBackDate" :class="this.btn_back_date_d == true ? 'btn btn-secondary disabled' : 'btn btn-primary'" @click="setDate('back')">
                                            <div><font-awesome-icon icon="backward" /></div>
                                        </button>

                                        <button class="btn btn-primary" @click="setDate('now')">
                                            <div><font-awesome-icon icon="angle-down" /></div>
                                        </button>

                                        <button class="btn btn-primary" @click="setDate('next')">
                                            <div><font-awesome-icon icon="forward" /></div>
                                        </button>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 1.1em; text-align: center">Čas rezervacije</div>
                                <div style="text-align: center">
                                    <span style="font-size: 1.2em"> {{ durResRen }} </span> <br />
                                </div>
                                <div style="text-align: center">
                                    <button id="btnBackDuration" :class="this.btn_back_duration_d == true ? 'btn btn-secondary disabled' : 'btn btn-primary'" @click="setDuration('back')">
                                        <div><font-awesome-icon icon="backward" /></div>
                                    </button>

                                    <button id="btnForwDuration" :class="this.btn_next_duration_d == true ? 'btn btn-secondary disabled' : 'btn btn-primary'" @click="setDuration('next')">
                                        <div><font-awesome-icon icon="forward" /></div>
                                    </button>
                                </div>
                            </td>
                            <td colspan="3">
                                <div style="font-size: 1.1em">Uporabnik</div>
                                <div>
                                    <model-list-select :list="usersLookup" v-model="userSelected" option-value="id" :custom-text="codeAndNameAndDesc" placeholder="Izberi uporabnika">
                                    </model-list-select>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 1.1em; margin-bottom: 10px; text-align: center">Višina tabele</div>
                                <div style="text-align: center">
                                    <button class="btn btn-primary" style="display: inline-block" @click="resizeTable('-')">
                                        <div><font-awesome-icon icon="minus" /></div>
                                    </button>
                                    <div style="display: inline-block">{{ this.heightTableAllBooks }}</div>
                                    <button class="btn btn-primary" style="display: inline-block" @click="resizeTable('+')">
                                        <div><font-awesome-icon icon="plus" /></div>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr id="idShowSearchAllBooks" style="display: none">
                            <td style="width: 25%">
                                <input style="width: 100%" type="text" v-model="allBooks_s_01" v-on:input="activeSearchingAllBooks()" placeholder="Išči naslov" />
                            </td>
                            <td style="width: 20%">
                                <input style="width: 100%" type="text" v-model="allBooks_s_02" v-on:input="activeSearchingAllBooks()" placeholder="Išči avtor" />
                            </td>
                            <td style="width: 10%">
                                <input style="width: 100%" type="text" v-model="allBooks_s_03" v-on:input="activeSearchingAllBooks()" placeholder="Išči leto" />
                            </td>

                            <td style="width: 15%">
                                <input style="width: 100%" type="text" v-model="allBooks_s_04" v-on:input="activeSearchingAllBooks()" placeholder="Išči kategorija" />
                            </td>

                            <td style="width: 10%" colspan="2"></td>
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
                                            <button
                                                :class="userSelected == '' ? 'btn btn-secondary disabled' : 'btn btn-primary'"
                                                data-toggle="collapse"
                                                v-bind:data-target="'#sh_allBooks' + book.ident"
                                                @click="showPodknjige(book.ident)"
                                            >
                                                <div><font-awesome-icon icon="pencil-alt" /></div>
                                            </button>
                                        </td>
                                        <td style="width: 10%; border-top: 0px"></td>
                                    </tr>

                                    <tr>
                                        <td colspan="7" class="hiddenRow" style="padding-top: 0px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; border-top: 0px">
                                            <div v-bind:id="'sh_allBooks' + book.ident" class="collapse" data-parent="#accordion2">
                                                <!-- tabela za prikazovanje urejanja -->
                                                <table id="podpodtable" style="width: 100%">
                                                    <tr>
                                                        <table id="tab_subBooks" class="table-light">
                                                            <thead style="background-color: #acc9e3; border-radius: 10px 10px 0px 0px">
                                                                <tr style="border-radius: 10px 10px 0px 0px">
                                                                    <td style="width: 24%; padding-left: 5px">Naslov</td>
                                                                    <td style="width: 15%">Izvod</td>
                                                                    <td style="width: 17%">Prost izbor (dnevi)</td>
                                                                    <td style="width: 9%">Termini</td>
                                                                    <td style="width: 15%">Zasedenost</td>
                                                                    <td style="width: 10%">Izposoja</td>
                                                                    <td style="width: 10%"></td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr class="table-secondary" v-for="(subBook, index) in subBooks" :key="index">
                                                                    <table>
                                                                        <thead>
                                                                            <tr>
                                                                                <td style="width: 24%; padding-left: 10px">{{ subBook.naslov }}</td>
                                                                                <td style="width: 15%; padding-left: 10px">{{ subBook.ident }} / {{ subBook.zap }}</td>
                                                                                <td style="width: 17%; padding-left: 10px">{{ subBook.pod_dniRazp }}</td>
                                                                                <td style="width: 9%; padding-left: 10px">{{ subBook.pod_termRazp }}</td>
                                                                                <td style="width: 15%; padding-left: 10px">
                                                                                    <div v-if="subBook.indikator == 5" title="Na voljo vsi termini" style="cursor: help">
                                                                                        <div style="background-color: #33cc33; width: 80%; border-radius: 20px">&nbsp;</div>
                                                                                    </div>

                                                                                    <div v-if="subBook.indikator == 4">
                                                                                        <div style="background-color: #cddb00; width: 80%; border-radius: 20px">&nbsp;</div>
                                                                                    </div>

                                                                                    <div v-if="subBook.indikator == 3">
                                                                                        <div style="background-color: #ffa200; width: 80%; border-radius: 20px">&nbsp;</div>
                                                                                    </div>

                                                                                    <div v-if="subBook.indikator == 2">
                                                                                        <div style="background-color: #ff5900; width: 80%; border-radius: 20px">&nbsp;</div>
                                                                                    </div>

                                                                                    <div v-if="subBook.indikator == 1" title="Ni terminov za rezervacijo / izposojo" style="cursor: help">
                                                                                        <div style="background-color: #cc0000; width: 80%; border-radius: 20px">&nbsp;</div>
                                                                                    </div>
                                                                                </td>
                                                                                <td style="width: 10%; padding-left: 30px; color: #2eb82e" v-if="subBook.pRenting == '1'">
                                                                                    <font-awesome-icon icon="check" />
                                                                                </td>
                                                                                <td style="width: 10%; padding-left: 30px; color: #e60000" v-if="subBook.pRenting == '0'">
                                                                                    <font-awesome-icon icon="times" />
                                                                                </td>
                                                                                <td style="width: 10%">
                                                                                    <button class="btn btn-primary" @click="toggleMonthDet('sh_sb_class' + subBook.ident + subBook.zap)">
                                                                                        <div><font-awesome-icon icon="pencil-alt" /></div>
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td colspan="7" style="padding-top: 0px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; border-top: 0px">
                                                                                    <div
                                                                                        v-bind:id="'sh_subBook' + subBook.ident + subBook.zap"
                                                                                        v-bind:class="'sh_sb_class' + subBook.ident + subBook.zap"
                                                                                        style="margin-top: 30px; margin-bottom: 30px; display: none"
                                                                                    >
                                                                                        <table style="margin-left: 3%; margin-right: 3%">
                                                                                            <tr>
                                                                                                <td
                                                                                                    v-bind:id="'btnMonthToogle' + subBook.ident + subBook.zap"
                                                                                                    @click="toggleSelTer('sh_tr_rezervacija' + subBook.ident + subBook.zap, false)"
                                                                                                >
                                                                                                    {{ subBook.monthName }}
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td v-for="(day, index2) in subBook.zasedenostDnevi" :key="index2" style="padding: 0px">
                                                                                                    <div v-if="day == 'X'" style="background-color: #ff3333; text-align: center; cursor: default">
                                                                                                        {{ index2 + 1 }}
                                                                                                    </div>
                                                                                                    <div
                                                                                                        v-if="day == 'R'"
                                                                                                        @click="
                                                                                                            changeReservation(
                                                                                                                subBook.ident,
                                                                                                                subBook.zap,
                                                                                                                index2 + 1,
                                                                                                                'sh_tr_rezervacija' + subBook.ident + subBook.zap
                                                                                                            )
                                                                                                        "
                                                                                                        style="background-color: #00b359; text-align: center; cursor: pointer"
                                                                                                    >
                                                                                                        {{ index2 + 1 }}
                                                                                                    </div>
                                                                                                    <div v-if="day == 'P'" style="background-color: gray; text-align: center; cursor: default">
                                                                                                        {{ index2 + 1 }}
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr v-bind:class="'sh_tr_rezervacija' + subBook.ident + subBook.zap" style="display: none">
                                                                                                <td>
                                                                                                    <div v-bind:id="'tr_rezervacija' + subBook.ident + subBook.zap">
                                                                                                        <span>Rezervacija od: </span> {{ subBook.r_datumOD }} -
                                                                                                        {{ subBook.r_datumDO }}
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <button
                                                                                                        v-bind:id="'btn_rezerviraj' + subBook.ident + subBook.zap"
                                                                                                        :class="subBook.r_datumOD == '' ? 'btn btn-secondary disabled' : 'btn btn-primary'"
                                                                                                        @click="
                                                                                                            reservation(
                                                                                                                subBook.r_datumOD,
                                                                                                                subBook.r_datumDO,
                                                                                                                subBook.ident,
                                                                                                                subBook.zap,
                                                                                                                subBook.naslov
                                                                                                            )
                                                                                                        "
                                                                                                    >
                                                                                                        <div><font-awesome-icon icon="book" />&nbsp; Rezerviraj</div>
                                                                                                    </button>
                                                                                                    <button
                                                                                                        v-if="subBook.pRenting == '1'"
                                                                                                        class="btn btn-primary"
                                                                                                        @click="renting(subBook.ident, subBook.zap, subBook.naslov)"
                                                                                                    >
                                                                                                        <div><font-awesome-icon icon="book-reader" />&nbsp; Izposodi</div>
                                                                                                    </button>
                                                                                                    <div v-if="subBook.prekoracitev == true" style="display: inline-block; color: red">
                                                                                                        &nbsp;&nbsp;<font-awesome-icon icon="exclamation-circle" />&nbsp; Knjiga presega rok izposoje in
                                                                                                        še ni bila vrnjena, zato je ni mogoče rezervirati / izposoditi.
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </table>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
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
                    <tfoot>
                        <button class="btn btn-outline-secondary btn_allBooksOpenClose" style="width: 100%" @click="openCloseTabAllBooks()">
                            <div><font-awesome-icon icon="arrows-alt-v" /></div>
                        </button>
                    </tfoot>
                </table>
            </div>
        </div>
        <div id="modalBlur" v-if="modalEnabled == true">
            <div id="modalInfo" v-click-outside="(e) => closeModal(e)">
                <div>
                    <div>
                        <button id="closeModal" type="button" @click="closeModal()" style="border: 0px; font-size: 1.6em; right: 10px; position: absolute; top: 10px">&times;</button>
                    </div>
                    <div style="border-bottom: 1px solid #d9d9d9; text-align: center">
                        <h4>{{ this.modalTitle }}</h4>
                    </div>
                    <div style="padding-top: 20px; padding-left: 20px">
                        <table id="modalTable">
                            <tr>
                                <td>
                                    <p>Knjiga:</p>
                                </td>
                                <td>
                                    <p>{{ this.AddResRent.naslov }}</p>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <p>Izvod:</p>
                                </td>
                                <td>
                                    <p>{{ this.AddResRent.id_ident }} / {{ this.AddResRent.id_zap }}</p>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <p>Datum od:&nbsp;</p>
                                </td>
                                <td>
                                    <p>{{ this.AddResRent.datum_od }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p>Datum do:&nbsp;</p>
                                </td>
                                <td>
                                    <p>{{ this.AddResRent.datum_do }}</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div style="margin-top: 20px; text-align: center; color: green; font-size: 1.5em">Uspešno izvedeno</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ModelListSelect } from 'vue-search-select';
export default {
    props: ['allResRen', 'allBooks', 'subBooks', 'dateNowP'],
    components: {},
    data: function () {
        return {
            usersLookup: [],
            userSelected: '',

            resRen_s_01: '',
            resRen_s_02: '',
            resRen_s_03: '',
            allBooks_s_01: '',
            allBooks_s_02: '',
            allBooks_s_03: '',
            allBooks_s_04: '',

            heightTableAllBooks: '',
            heightTableResRen: '',
            tableResRen_closed: false,
            tableAllBooks_closed: false,
            btn_back_date_d: true,
            btn_back_duration_d: true,
            btn_next_duration_d: false,
            ident: '',
            termDate: '',
            durResRen: 10,
            modalTitle: '',
            modalEnabled: false,
            checkBoxToggle: 0,

            AddResRent: {
                id_ident: '',
                id_zap: '',
                id_member: '',
                datum_od: '',
                datum_do: '',
                status: '',
                naslov: ''
            }
        };
    },

    methods: {
        // ** pridobi člane knjižnice, da lahko knjižničar izbere komu dodeliti določeno knjigo
        getUsers() {
            axios
                .get('/resRent/users')
                .then((response) => {
                    this.usersLookup = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        // ** Prikaz informacij v spustnem meniju
        codeAndNameAndDesc(item) {
            return `${item.name} - ${item.emso}`;
        },

        selectOption2() {
            this.userSelected = this.usersLookup[0].code;
        },

        //? status knjige -> 0- rezervacija oz izposoja končana; 1-rezervirana; 2-rezervacija in izposoja, 3-izposoja
        // ** Funkcija za preklop med stanji knjige, ki je v izposoji / rezervaciji
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
                        this.$emit('reloadResRen');
                        this.$emit('reloadAllBooks');
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        // ** prikaže nastavitve tabele
        showOptions() {
            this.getUsers();
            if (this.heightTableAllBooks == '') {
                this.heightTableAllBooks = $('#tableAllBooks').css('max-height');
            }

            if ($('#idShowOptions').css('display') == 'none') {
                $('#idShowOptions').show();
            } else $('#idShowOptions').hide();
        },

        // ** Nastavi obdobje
        setDate($method) {
            var dateNow = new Date();
            var months = ['Januar', 'Februar', 'Marec', 'April', 'Maj', 'Junij', 'Julij', 'Avgust', 'September', 'Oktober', 'November', 'December'];

            if ($method == 'now') {
                this.termDate = this.dateNowP.dateDefault;
                $('#id_dateDisplay').text(this.dateNowP.dateDisplay);
                this.btn_back_date_d = true;
            } else if ($method == 'next') {
                var dateCurrent = new Date(this.termDate == '' || this.btn_back_date_d == true ? this.dateNowP.dateFirstDay : this.termDate);
                var dateNext = new Date(dateCurrent.getFullYear(), dateCurrent.getMonth() + 1, dateCurrent.getDate());
                this.termDate = dateNext.getFullYear() + '-' + ('0' + (dateNext.getMonth() + 1)).slice(-2) + '-' + ('0' + dateNext.getDate()).slice(-2);
                $('#id_dateDisplay').text(months[dateNext.getMonth()] + ', ' + dateNext.getFullYear());
                this.btn_back_date_d = false;
            } else if ($method == 'back') {
                var dateCurrent = new Date(this.termDate);
                var dateBack = new Date(dateCurrent.getFullYear(), dateCurrent.getMonth() - 1, dateCurrent.getDate());
                $('#id_dateDisplay').text(months[dateBack.getMonth()] + ', ' + dateBack.getFullYear());

                if (dateBack.getFullYear() == dateNow.getFullYear() && dateBack.getMonth() + 1 == dateNow.getMonth() + 1) {
                    this.btn_back_date_d = true;
                    $('#id_dateDisplay').text(('0' + dateNow.getDate()).slice(-2) + '.' + ('0' + (dateNow.getMonth() + 1)).slice(-2) + '.' + dateNow.getFullYear());
                }

                this.termDate = dateBack.getFullYear() + '-' + ('0' + (dateBack.getMonth() + 1)).slice(-2) + '-' + ('0' + dateBack.getDate()).slice(-2);
            }

            if (this.ident != '') {
                var relInfoSubBooks = {
                    id_ident: this.ident,
                    period: this.termDate,
                    duration: this.durResRen
                };
                this.$emit('reloadSubBooks', relInfoSubBooks);
            }
        },

        // ** nastavitev časa izposoje
        setDuration($method) {
            if ($method == 'back') {
                this.durResRen = this.durResRen - 10;
                if (this.durResRen == '10') {
                    this.btn_back_duration_d = true;
                } else {
                    this.btn_back_duration_d = false;
                    this.btn_next_duration_d = false;
                }
            } else if ($method == 'next') {
                this.durResRen = this.durResRen + 10;
                if (this.durResRen == '30') {
                    this.btn_next_duration_d = true;
                } else {
                    this.btn_back_duration_d = false;
                    this.btn_next_duration_d = false;
                }
            }

            // ** Če je določena knjiga odprta, ponovno naloži njene podknjige oz izvode z informacijami o prostih terminih in rezervacijami / izposojami
            if (this.ident != '') {
                var relInfoSubBooks = {
                    id_ident: this.ident,
                    period: this.termDate,
                    duration: this.durResRen
                };
                this.$emit('reloadSubBooks', relInfoSubBooks);
            }
        },

        // ** prikaže izvode določene knjige **
        showPodknjige(ident_) {
            if (this.termDate == '') {
                this.termDate = this.dateNowP.dateDefault;
            }
            // ** Izvede se reload, če kliknemo na knjigo z drugačnim identifikatorjem, sicer se ne proži funkcija nalaganja (npr. zapiranje prikaza podknjig)
            if (this.ident != ident_) {
                this.ident = ident_;
                var relInfoSubBooks = {
                    id_ident: ident_,
                    period: this.termDate,
                    duration: this.durResRen
                };

                this.$emit('reloadSubBooks', relInfoSubBooks);
            } else {
                this.ident = '';
            }
        },

        // ** nastavi datum od - do za rezervacijo
        changeReservation: function (id_ident_, id_zap_, dan_, $ide_) {
            var tabDates = {
                id_ident: id_ident_,
                id_zap: id_zap_,
                day: dan_,
                period: this.termDate,
                duration: this.durResRen
            };

            this.$emit('changeReservation', tabDates);
            this.toggleSelTer($ide_, true);
        },

        // ** Prikaže / skrije podatke o prostih terminih in možnost rezerviranja oz izposoje
        toggleMonthDet($ide) {
            if ($('.' + $ide).css('display') == 'none') {
                $('.' + $ide).css('display', 'block');
            } else {
                $('.' + $ide).css('display', 'none');
            }
        },

        // ** Prikaže / skrije izbran termin
        toggleSelTer($ide, $dayClicked) {
            if ($dayClicked == true) $('.' + $ide).css('display', 'block');
            else {
                if ($('.' + $ide).css('display') == 'none') {
                    $('.' + $ide).css('display', 'block');
                } else {
                    $('.' + $ide).css('display', 'none');
                }
            }
        },

        // ? 0- rezervacija oz izposoja končana; 1-rezervirana; 2-rezervacija in izposoja, 3-izposoja
        // ** izvede izposojo določenega izvoda knjige ** //
        renting: function (id_ident_, id_zap_, naslov_) {
            // ** nastavitev datuma od - do za izposojo
            var dateFrom = new Date();
            var dateTo = new Date(dateFrom);
            dateTo.setDate(dateTo.getDate() + this.durResRen - 1);

            // ** Nastavitev podatkov, ki se pošljejo v shranjevanje (status 3 = izposoja)
            (this.AddResRent.id_ident = id_ident_),
                (this.AddResRent.id_zap = id_zap_),
                (this.AddResRent.id_member = this.userSelected),
                (this.AddResRent.status = 3),
                (this.AddResRent.datum_od = ('0' + dateFrom.getDate()).slice(-2) + '.' + ('0' + (dateFrom.getMonth() + 1)).slice(-2) + '.' + dateFrom.getFullYear()),
                (this.AddResRent.datum_do = ('0' + dateTo.getDate()).slice(-2) + '.' + ('0' + (dateTo.getMonth() + 1)).slice(-2) + '.' + dateTo.getFullYear()),
                (this.AddResRent.naslov = naslov_);

            axios
                .post('/resRent/store', {
                    AddResRent: this.AddResRent
                })
                .then((response) => {
                    if (response.status == 201) {
                        var relInfoSubBooks = {
                            id_ident: this.AddResRent.id_ident,
                            period: this.termDate,
                            duration: this.durResRen
                        };

                        // ** Pri uspešnem shranjevanju se izvede reload podknjig (izvodov) in seznam knjig z aktivnim statusom rezervacije in izposoje
                        this.$emit('reloadSubBooks', relInfoSubBooks);
                        this.$emit('reloadResRen');

                        // ** Prikažemo modalno okno za izposojo
                        this.modalTitle = 'Izposoja';
                        this.modalEnabled = true;
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        // ? 0- rezervacija oz izposoja končana; 1-rezervirana; 2-rezervacija in izposoja, 3-izposoja
        // ** izvede rezervacijo določenega izvoda knjige **
        reservation: function (datum_od_, datum_do_, id_ident_, id_zap_, naslov_) {
            // ** Nastavitev podatkov, ki se pošljejo v shranjevanje (status 1 = rezervacija)
            (this.AddResRent.id_ident = id_ident_),
                (this.AddResRent.id_zap = id_zap_),
                (this.AddResRent.id_member = this.userSelected),
                (this.AddResRent.datum_od = datum_od_),
                (this.AddResRent.datum_do = datum_do_),
                (this.AddResRent.status = 1),
                (this.AddResRent.naslov = naslov_);

            axios
                .post('/resRent/store', {
                    AddResRent: this.AddResRent
                })
                .then((response) => {
                    if (response.status == 201) {
                        var relInfoSubBooks = {
                            id_ident: this.AddResRent.id_ident,
                            period: this.termDate,
                            duration: this.durResRen
                        };

                        // ** Pri uspešnem shranjevanju se izvede reload podknjig (izvodov) in seznam knjig z aktivnim statusom rezervacije in izposoje
                        this.$emit('reloadSubBooks', relInfoSubBooks);
                        this.$emit('reloadResRen');

                        // ** Prikažemo modalno okno za izposojo
                        this.modalTitle = 'Rezervacija';
                        this.modalEnabled = true;
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        // ** Prikaže možnost iskanja v tabeli
        showSearchResRen() {
            if (this.heightTableResRen == '') {
                this.heightTableResRen = $('#tableResRen').css('max-height');
            }
            if ($('#idShowSearchResRen').css('display') == 'none') {
                $('#idShowSearchResRen').show();
            } else $('#idShowSearchResRen').hide();
        },

        // ** Prikaže možnost iskanja v tabeli
        showSearchAllBooks() {
            if ($('#idShowSearchAllBooks').css('display') == 'none') {
                $('#idShowSearchAllBooks').show();
            } else $('#idShowSearchAllBooks').hide();
        },

        // ** Funkcija, ki iskalne nize emita v njeno nadrejeno .vue, kjer se lahko izvede filtracija na določenem polju (tabela, ki prikazuje aktivne rezervacije in izposoje)
        activeSearchingResRen: function ($typeFilter) {
            if ($typeFilter == 'checkBox' && this.checkBoxToggle == 0) this.checkBoxToggle = 1;
            else if ($typeFilter == 'checkBox' && this.checkBoxToggle == 1) this.checkBoxToggle = 0;

            var tabSearchResRen = {
                tab1: this.resRen_s_01,
                tab2: this.resRen_s_02,
                tab3: this.resRen_s_03,
                checkBox: this.checkBoxToggle
            };

            this.$emit('searchChangesResRen', tabSearchResRen);
        },

        // ** Funkcija, ki iskalne nize emita v njeno nadrejeno .vue, kjer se lahko izvede filtracija na določenem polju (tabela, ki prikazuje vse knjige)
        activeSearchingAllBooks: function () {
            var tabSearchAllBooks = {
                tab1: this.allBooks_s_01,
                tab2: this.allBooks_s_02,
                tab3: this.allBooks_s_03,
                tab4: this.allBooks_s_04
            };

            this.$emit('searchChangesAllBooks', tabSearchAllBooks);
        },

        // ** Nastavitev največje velikosti tabele. Uporabnik si lahko prilagodi tabelo
        resizeTable($method_) {
            if ($method_ == '+') {
                var increase = parseInt(this.heightTableAllBooks) + 100;
                $('#tableAllBooks').css('max-height', increase);
                this.tableAllBooks_closed = false;
            } else if ($method_ == '-') {
                var decrease = parseInt(this.heightTableAllBooks) - 100;
                $('#tableAllBooks').css('max-height', decrease);
                this.tableAllBooks_closed = false;
            }
            this.heightTableAllBooks = $('#tableAllBooks').css('max-height');
        },

        // ** Nastavitev največje velikosti tabele. Uporabnik si lahko prilagodi tabelo
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

        // ** V nogi tabele lahko tabelo zapremo s pritiskom na gumb ali pa jo odpremo
        openCloseTabAllBooks() {
            if (this.tableAllBooks_closed == true) {
                $('#tableAllBooks').css({ maxHeight: this.heightTableAllBooks });
                this.tableAllBooks_closed = false;
            } else {
                if (this.heightTableAllBooks == '') {
                    this.heightTableAllBooks = $('#tableAllBooks').css('max-height');
                }
                $('#tableAllBooks').css({ maxHeight: 0 });
                this.tableAllBooks_closed = true;
            }
        },

        // ** V nogi tabele lahko tabelo zapremo s pritiskom na gumb ali pa jo odpremo
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
        },

        // ** Zapre modalno okno
        closeModal() {
            this.modalEnabled = false;
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
.disabled {
    pointer-events: none;
    opacity: 0.3;
}

.btn.btn-primary[disabled] {
    background-color: #818181;
    backdrop-filter: blur(2px);
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
    overflow-y: hidden;
    display: unset;
    height: auto;
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
    width: 430px;
    height: 250px;
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

.btn_allBooksOpenClose {
    background-color: rgb(133 186 211) !important;
}
</style>
