<template>
    <div>
        <div class="d-flex justify-content-between mb-3">
            <div class="d-flex align-items-center">
                <label class="text-nowrap mr-2 mb-0">Nbre de ligne par Page</label>
                <select class="form-control form-control-sm" v-model="paginate">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                </select>
            </div>
            <div class="custom-select">
                <input
                  type="text"
                  v-model="searchQuery"
                  placeholder="Rechercher un locataire..."
                  @click="toggleDropdown"
                  @focus="showDropdown = true"
                  @blur="closeDropdown"
                  class="form-control"
                />
                <div v-if="showDropdown" class="dropdown">
                  <ul>
                    <li
                      v-for="locataire in filteredLocataires"
                      :key="locataire.id"
                      @mousedown="selectLocataire(locataire.locat_id)"
                    >
                      {{ locataire.locat_nom }} {{ locataire.locat_prenom }}
                    </li>
                  </ul>
                </div>
          </div>
          <button class="btn btn-success mb-3" data-toggle="modal" data-target="#paiement" v-on:click="newPaiement()"><i class="fa fa-plus"></i> Ajouter un paiement</button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
              <thead class="bg-white">
                <tr>
                    <th>Locataire</th>
                    <th>Local</th>
                    <th>Propriétaire</th>
                    <th>Loyer</th>
                    <th>Montant Total</th>
                    <!--th>Echéance</th-->
                    <th>Status</th>
                    <th class="text-right">Action</th>
                </tr>
                <tr>
                    <th colspan="9" class="position-relative p-0">
                        <div class="loader-line" :class="[isLoadingTab?'d-block':'d-none']"></div>
                    </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="oper in operations.data" :key="oper.identifiant" :class="canPay.includes(oper.identifiant)?'':'tr-disabled'">
                    <td class="align-middle">
                        <div class="contentPerson">
                            <div class="avatarPerson">
                                <i class="nav-icon fas fa-user"></i>
                            </div>
                            <div class="nameFormat">
                                <span class="namePerson">{{oper.locat_nom}} {{oper.locat_prenom}}</span>
                                <span class="contactPerson">john@creative-tim.com</span>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle">
                        <ul class="m-0 p-0">
                            <li v-for="local in oper.locaux">{{ local.local_type_local }}({{ local.local_type_location }})</li>
                        </ul>

                    </td>
                    <td class="align-middle">
                        {{oper.proprio_nom}} {{oper.proprio_prenom}}
                    </td>
                    <td class="align-middle">{{ oper.paiement_mois_location }}</td>
                    <td class="align-middle"><strong>{{ helper_separator_amount(oper.bail_montant_ht) }}</strong></td>
                    <!--td class="align-middle">{{ oper.paiement_echeance | formatDateFR }}</td-->
                    <td class="align-middle">
                        <label class="badgeLabel" :class="[oper.paiement_etat==3?'badgeSuccess':'', oper.paiement_etat==2?'badgeOrange':'', oper.paiement_etat==0?'badgeBlue':'']">{{ etatPaiement[oper.paiement_etat] }}</label>
                        <span style="font-size: 20px;" v-if="oper.paiement_user=='SYSTEM'" :title="oper.paiement_user">🤖</span>
                        <span style="font-size: 20px;" v-else  :title="oper.paiement_user">👤</span>
                    </td>
                    <td class="text-right align-middle">
                        <button class="actionType1 mr-3" data-toggle="modal" data-target="#paiement" v-on:click="editLoyer(oper)" >💰<span class="badge border border-warning badge-light position-absolute total-right-corner">{{ Array.isArray(oper.paiements)? oper.paiements.length : 0}}</span></button>
                        <button class="actionType1 mr-3 mr-3" :title="oper.paiements_url_quittance=='' || oper.paiements_url_quittance==null ? 'Pas de quittance' : 'Voir la quittance'" :disabled="oper.paiements_url_quittance=='' || oper.paiements_url_quittance==null" v-on:click="showQuittance(oper.paiements_url_quittance)">{{ oper.paiements_url_quittance=='' || oper.paiements_url_quittance==null ? '❗' : '📜' }} </button>
                        <button :disabled="Array.isArray(oper.paiements)? oper.paiements.length > 0?true:false : false" :class="[Array.isArray(oper.paiements)? oper.paiements.length > 0? 'disabledRem':false : false]" class="actionType1" @click="deletePaiement(oper)">❌</button>
                    </td>
                </tr>
              </tbody>
            </table>
        </div>
        <div class="d-flex mt-4 justify-content-center">
            <pagination
                :data="operations"
                :limit=10
                @pagination-change-page="getOperations"
            ></pagination>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="paiement" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="paiement" aria-hidden="true">

            <div class="modal-dialog modal-xl position-relative" role="document">
                <div class="loader-line" :class="[isLoading?'d-block':'d-none']"></div>
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-uppercase">
                        <template v-if="isNewPaiment">
                            <strong><span class="text-primary"><u>Ajouter</u></span> un paiment loyer</strong>
                        </template>
                        <template v-else>
                             <strong><span class="text-primary"><u>Editer</u></span> un loyer</strong>
                        </template>

                    </h5>



                  <button type="button" class="close" ref="closePopup" data-dismiss="modal" aria-label="Close" @click="close()"><span aria-hidden="true">&times;</span></button>
                </div>
                <template v-if="isNewPaiment">
                <form @submit.prevent="createPaiementApi()">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="d-block text-uppercase text-info border-bottom titleform border-info">Infos Locataire</label>
                            </div>
                        </div>
                        <div class="row  mt-3">
                             <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Choisir un locataire</label>
                                        <LocataireSelect
                                              :list="listLocataireTab"
                                              placeholder="Choisir un locataire"
                                              :errorValidation="{ 'border-danger': manualIsSubmitted && !$v.manualLoyerForm.locataire.required }"
                                              @selected="onLocataireChoisi"
                                            />
                                    </div>
                            </div>

                            <div class="col-md-6">

                                    <div class="form-group">
                                        <label>Choisir le local loué</label>
                                        <select class="form-control" v-model="manualLoyerForm.local" :class="{ 'border-danger': manualIsSubmitted && !$v.manualLoyerForm.local.required }">
                                            <option>Choisir un local</option>
                                            <option v-for="local in localActif.data" :key="local.identifiant" @click="setInfosLocation(local)" :value="local.local_id" >{{ local.local_type_local}} ({{ local.local_type_location}}), {{ local.bien_adresse}} N°{{ local.bien_numero }} - {{ local.bien_ville }} ({{ local.bien_pays }})</option>
                                        </select>
                                    </div>

                            </div>
                        </div>
                        <div class="row mt-2">
                             <div class="col-md-6">
                                <div class="form-group">
                                    <label>Montant loyer</label>
                                    <div class='px-2 py-1'><strong>{{manualLoyerForm.montantLoyer? helper_separator_amount(manualLoyerForm.montantLoyer):manualLoyerForm.montantLoyer }}</strong></div>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mois à payer</label>
                                   <date-picker
                                      v-model="manualLoyerForm.moisLoyer"
                                      type="month"
                                      format="MM/YYYY"
                                      value-type="format"
                                      input-class="form-control w-100"
                                      :class="['w-100', { 'border-danger-date': manualIsSubmitted && !$v.manualLoyerForm.moisLoyer.required }]"
                                      placeholder="Choisir un mois"
                                    />

                                </div>
                            </div>


                        </div>



                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="submit" class="btn btn-success">Ajouter</button>
                        <button type="button" class="btn btn-secondary " ref="closePopup" data-dismiss="modal" aria-label="Close" @click="reset()">Fermer</button>
                    </div>
                  </form>
                 </template>
                  <template v-else>
                    <div class="containerLoyer" v-if="currentLoyer">
                         <div class='formAddPayment' :class="showFormPayment?'actifForm':''" v-if="showFormPayment">
                                <h3>💳 Nouveau paiement</h3>
                                <div>
                                    <div :style="!infoLocataire.locat_avoir > 0?'opacity: .5':'opacity: 1'">
                                        <label class="containerCheckbox">Utiliser mon solde disponible
                                          <input type="checkbox"  v-model="addPaimentForm.useSolde" :disabled="!(infoLocataire.locat_avoir > 0)">
                                          <span class="checkmark"></span>
                                        </label>


                                        <p v-if="addPaimentForm.useSolde">→ Reste à payer : {{ helper_separator_amount(calculMntRestant(infoLocataire.loyer_hors_charge, infoLocataire.locat_avoir)) }} FCFA</p>

                                    </div>
                                </div>
                                <div>
                                    <div class="form-group mb-3">
                                        <label>Montant à payer: <span class="required">*</span></label>
                                        <input
                                            v-model="addPaimentForm.paiementMnt"
                                            placeholder="Saisir un montant"
                                            class="form-control"
                                            :disabled="addPaimentForm.useSolde && calculMntRestant(infoLocataire.loyer_hors_charge, infoLocataire.locat_avoir) <= 0"
                                            :class="{ 'border-danger': isPaymentSubmitted && !$v.addPaimentForm.paiementMnt.required }"
                                            type="number"
                                            />
                                    </div>

                                    <div class="form-group mb-3" v-if="!(addPaimentForm.useSolde && calculMntRestant(infoLocataire.loyer_hors_charge, infoLocataire.locat_avoir) <= 0)">
                                        <label>Moyen de paiement: <span class="required">*</span></label>
                                        <select id="payment-method"  class="form-control" :class="{ 'border-danger': isPaymentSubmitted && !$v.addPaimentForm.paiementType.required }" v-model="addPaimentForm.paiementType">
                                            <option value="">Choisir un moyen de paiement</option>
                                            <option value="Virement bancaire">Virement bancaire</option>
                                            <option value="Espèces">Espèces</option>
                                            <option value="Carte bancaire">Carte bancaire</option>
                                            <option value="Wallet">Wallet</option>
                                        </select>
                                    </div>

                                     <div class="form-group mb-3">
                                        <label>Joindre un document justificatif:</label>
                                        <input class="d-block" ref="attachDocJustif"  type="file" v-on:change="handleFileUpload()"/>
                                    </div>
                                </div>
                                <div class="actionPaiement">
                                    <button type="button" class="d-flex align-items-center btn btn-sm btn-success ml-2 text-nowrap" :disabled="paymentRun" style="width: auto; gap: 8px" @click="addPaimentAPI()">
                                        <span v-if="paymentRun" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        <span v-else><i class="fa fa-check"></i></span><span>Valider</span></button>
                                    <button type="button" class="btn btn-sm btn-secondary ml-2 text-nowrap" style="width: auto;" @click="showFormPaiement(false)"><i class="fa fa-times"></i>  Annuler</button>
                                </div>
                            </div>

                        <section class="card" :class="showFormPayment?'disabledDiv':''">
                            <h2>🏠 Informations du Locataire</h2>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p><strong>Nom :</strong> {{ infoLocataire.locataire  }} </p>
                                    <p><strong>Bien Loué :</strong> <span v-if="infoLocataire.local.length > 0">
                                            <span v-for="(local, index) in infoLocataire.local">
                                            {{ local.local_type_local }} –
                                            {{ local.local_type_location }}
                                            </span>
                                        </span> </p>
                                    <p><strong>Période :</strong> {{infoLocataire.date_du}} ~ {{infoLocataire.date_au}}</p>
                                    <p><strong>Montant Total :</strong> {{infoLocataire.loyer_hors_charge}} FCFA</p>
                                </div>

                                <div>
                                    <p class="border pl-2 pt-1">Solde disponible : <strong>{{ helper_separator_amount(infoLocataire.locat_avoir) }}</strong> FCFA  </p>

                                    <p><button class="btn">🔍 Voir l’historique des versements</button></p>
                                </div>
                            </div>

                        </section>

                        <section class="card" :class="showFormPayment?'disabledDiv':''">
                            <div class="d-flex align-items-center justify-content-between">
                                <h2>⏳ Historique des paiements</h2>
                                <div class="actions" :class="showFormPayment?'disabledDiv':''">
                                    <template v-if="(calculMontantPaiement() < parseInt(infoLocataire.loyer_hors_charge.replace(/\s+/g, ''))) || (currentLoyer.paiements_url_quittance=='' || currentLoyer.paiements_url_quittance==null)">
                                        <button
                                                type="button"
                                                class="btn btn-primary"
                                                @click="showFormPaiement(true)"

                                              >
                                               <i class="fa fa-plus"></i> Effectuer un paiement
                                        </button>

                                    </template>
                                    <template v-else>
                                        <button id="download-receipt" class="btn btn-success"  v-on:click="showQuittance(currentLoyer.paiements_url_quittance)"><i class="fa fa-download"></i> Télécharger la quittance</button>
                                    </template>


                                </div>
                            </div>


                            <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ccc;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead style="background-color: #f8f8f8; position: sticky; top: 0; z-index: 10;">
                                        <tr>
                                            <th>Date</th>
                                            <th>Montant</th>
                                            <th>Mode de paiement</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody id="payments-table">
                                        <tr v-for="(paiment, index) in paiements" :key="index">
                                            <td>{{paiment.paiementDate}}</td>
                                            <td>{{paiment.paiementMontant}} FCFA</td>
                                            <td>{{paiment.paiementType}} </td>
                                            <td class="status received"><button type="button" class="btn btn-sm btn-danger ml-2 text-nowrap" style="width: auto;" @click="showQuittance(paiment.url_recu)"><i class="fa fa-file-pdf"></i> Voir le reçu</button></td>
                                        </tr>
                                        <tr v-if="paiements.length == 0"><td colspan="4">Aucun paiement!</td></tr>

                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <section class="card" :class="showFormPayment?'disabledDiv':''">
                            <h2>💰 État du paiement</h2>
                            <div class="progress-bar">
                                <div class="progressLine" id="progressLine" :style="{ width: (calculMontantPaiement() / parseInt(infoLocataire.loyer_hors_charge.replace(/\s+/g, ''))) * 100 + '%' }"></div>
                            </div>
                            <p><strong id="paid-amount">{{calculMontantPaiement()}} FCFA</strong> payé sur <strong>{{infoLocataire.loyer_hors_charge}} FCFA</strong></p>
                            <p v-if="mntRestantAPayer >= 0" class="remaining">Montant restant : <span id="remaining-amount">{{mntRestantAPayer}} FCFA</span></p>
                            <p v-else class="remaining">Montant versé : <span id="remaining-amount">{{-1 * mntRestantAPayer}} FCFA</span></p>
                        </section>


                    </div>
                    </template>
                </div>
            </div>
        </div>
        <modalFile></modalFile>
    </div>
</template>

<script>
import DatePicker from 'vue2-datepicker';
import VueCountryDropdown from 'vue-country-dropdown';
import 'vue2-datepicker/index.css';
import { EventBus } from "../../event-bus";
import { required, email, minLength, between } from 'vuelidate/lib/validators';
import modalFile from '../../components/modal/viewFile.vue';


// Pdf
import { PdfMakeWrapper, Table, QR, Img} from 'pdfmake-wrapper';

import { ITable } from 'pdfmake-wrapper/lib/interfaces';

import pdfFonts from "pdfmake/build/vfs_fonts";
import LocataireSelect from "../../components/utils/LocataireSelect.vue";

export default {
    name: "Operations",
    props: ["listProprio", "listLocataire", "etatPaiement"],
    components: { DatePicker, VueCountryDropdown, modalFile, LocataireSelect},
    data () {
        return {
            editmode: false,
            isLoading: false,
            isSubmitted: false,
            isLoadingTab: false,
            isNewPaiment: false,
            operations : {},
            reste: 0,
            avoir: 0,
            infoLocataire: {
                id : '',
                locataire: '',
                montant_ht: '',
                date_du: '',
                date_au: '',
                charge: '',
                loyer_hors_charge: '',
                local: [],
                echeance: '',
                loyer: 'Loyer mai 2023',
                fichier: null,
                proprio_id: '',
                locat_avoir: '',
                locataireChoosen: null
            },
            id_loyer: "",
            paiement_bail_id: "",
            etatLoyer: "",
            paiements: [],
            paginate: 5,

            currentLoyer: null,
            selectedLocataireId: '',
            searchQuery: "",
            showDropdown: false,
            listLocataireTab: [],
            localActif: {},
            isLodingLocalActif: false,
            months: [],
            addPaimentForm: {
                paiementMnt:'',
                paiementFichier: null,
                paiementType: '',
                attachmentsFiles: [],
                useSolde: false,
                resteApayer:0
            },
            mntRestantAPayer: 0,
            showFormPayment: false,
            canPay: [],
            currentPage: 1,
            isPaymentSubmitted: false,
            paymentRun: false,
            manualLoyerForm: {
                locataire: null,
                local: null,
                moisLoyer: null,
                montantLoyer: null,
                bail_id: null,
                local_id: null
            },
            findBailByLocal: [],
            manualIsSubmitted: false,
            localSelected: null
        }
    },
    validations: {
        form : {
            proprio:     { required }


        },
        addPaimentForm: {
            paiementMnt: { required },
            paiementType: { required }
        },
        manualLoyerForm: {
            locataire: { required },
            local: { required },
            moisLoyer: { required }
        }

    },
    watch: {
       paiements: {
             handler: function(newValue) {
                this.mntRestantAPayer = parseInt(this.infoLocataire.loyer_hors_charge.replace(/\s+/g, '')) - this.calculMontantPaiement();
                if(this.etatLoyer == 3){ // Si PAYE
                    this.reste = 0;
                    if(this.locat_avoir > 0){
                        this.avoir = this.abs(this.locat_avoir);
                    }
                }else{
                    let total = 0, total_not_validate=0;
                    this.avoir = 0;


                    for(var i=0; i<newValue.length; i++){
                        total += parseInt(newValue[i].paiementMontant);
                        if(!newValue[i].validate){
                            total_not_validate+=newValue[i].paiementMontant;
                        }
                    }

                    if((this.infoLocataire.loyer_hors_charge - total) < 0){
                        if(!this.infoLocataire.validate){
                            this.avoir = this.abs(this.infoLocataire.loyer_hors_charge - total);
                        }else{
                            this.avoir = this.locat_avoir;
                        }

                    }else{
                        this.avoir = 0;

                    }

                    if(total >= this.infoLocataire.loyer_hors_charge){
                        this.reste = 0;
                    }else{
                        this.reste = parseInt(this.infoLocataire.loyer_hors_charge) - total;
                    }
                }



            },
            deep: true
       },
       operations: {
        handler: async function(newValue) {

            if(this.currentLoyer){
                const curr = newValue.data.find(p => p.identifiant === this.currentLoyer.identifiant) || null;
                this.editLoyer(curr)
            }
                // Ajouter la vérification du paiement pour chaque opération
            for (const oper of this.operations.data) {
                const resp = await this.checkCanPayLoyer(oper);
                if(resp){
                    this.canPay.push(oper.identifiant)
                }

                console.log(">> can", this.canPay)
            }
        }
       }, deep: true,
       paginate: function(){
            this.getOperations();
       },

    },
    methods: {
       newPaiement(){
        this.isNewPaiment = true
       },
       calculMntRestant(loyer, avoir){
        const mnt = parseInt(loyer.replace(/\s+/g, '')) - this.calculMontantPaiement() - avoir;
        return mnt <= 0 ? 0 : mnt;
       },
       calculMontantPaiement(){
            var mntTotal = 0;
            for(var i=0; i<this.paiements.length; i++){

               mntTotal+=parseInt(this.paiements[i].paiementMontant);
           }

           return mntTotal;
       },
       getOperations(page=1){
            this.currentPage = page;
            console.log(">>>", this.selectedLocataireId);

           this.isLoadingTab = true;
            axios.get("/operations/paiement_loyer?paginate="+this.paginate+'&page=' + page+'&locataireFiltre='+ this.selectedLocataireId).then(responses => {
               console.log(responses);
               this.operations = responses.data;
               this.isLoadingTab = false;
               // veirifier si current n'est pas null
               if(this.currentLoyer){
                const curr = this.operations.data.find(p => p.identifiant === this.currentLoyer.identifiant) || null;
                //editLoyer(curr)

               }



            }).catch(errors => {

            // react on errors.

            })
       },
       newModal(){

       },
       handleFileUpload(){
            this.addPaimentForm.attachmentsFiles = [];
            for(var i=0; i<this.$refs.attachDocJustif.files.length;i++){
                this.addPaimentForm.attachmentsFiles.push(this.$refs.attachDocJustif.files[i])
            }
        },
       reset(){
        this.paiements = [];
        this.paiements['paiementMontant'] = 0;
        this.paiements['paiementDate'] = new Date().toISOString().slice(0,10);
        this.paiements['validate'] = false;
        this.paiements['is_avoir'] = false;
        this.currentLoyer = null;
        this.close();

       },
       reserFormAdd(){
        this.manualLoyerForm.locataire = null;
        this.manualLoyerForm.local= null;
        this.manualLoyerForm.moisLoyer= null;
        this.manualLoyerForm.montantLoyer= null;
        this.manualLoyerForm.bail_id= null;
        this.manualLoyerForm.lacal_id= null;

       },
       editLoyer(oper){

        this.currentLoyer = oper;
        console.log("Loyy", this.currentLoyer )
        this.infoLocataire.id                = oper.bail_locataire;
        this.infoLocataire.locataire         = oper.locat_nom+' '+oper.locat_prenom;
        this.infoLocataire.loyer_hors_charge = this.helper_separator_amount(oper.bail_montant_ht);
        this.infoLocataire.echeance          = oper.paiement_echeance;
        this.infoLocataire.date_du           = oper.periode_du;
        this.infoLocataire.date_au           = oper.periode_au;
        this.infoLocataire.local             = oper.locaux;
        this.infoLocataire.proprio_id        = oper.proprio_id;
        this.infoLocataire.locat_avoir       = oper.locat_avoir;
        this.locat_avoir                     = oper.locat_avoir;
        this.id_loyer = oper.identifiant;
        this.paiement_bail_id = oper.paiement_bail_id;
        this.etatLoyer = oper.paiement_etat;


        if(oper.paiements){
            this.paiements = oper.paiements.sort((a, b) => new Date(b.paiementDate) - new Date(a.paiementDate));
        }else{
            this.paiements = [];
        }

        this.mntRestantAPayer = parseInt(this.infoLocataire.loyer_hors_charge.replace(/\s+/g, '')) - this.calculMontantPaiement()
       /* if(parseInt(this.locat_avoir) > 0 && this.etatLoyer!=3){
        this.paiements.push({
                paiementMontant: this.locat_avoir,
                paiementDate: new Date().toISOString().slice(0,10),
                validate: false,
                is_avoir: true
              });
        }*/

       },
       addPaiment(){
        this.paiements.push({
            paiementMontant: "",
            paiementDate: new Date().toISOString().slice(0,10),
            validate: false
          });
       },
       showFormPaiement(action){
        this.showFormPayment = action;
       },
       addMore() {
          this.paiements.push({
            paiementMontant: "",
            paiementDate: new Date().toISOString().slice(0,10),
            validate: false
          });
        },
        remove(index) {
          this.paiements.splice(index, 1);
        },
         createPaiementApi(){

            this.manualIsSubmitted = true;

            // stop here if form is invalid
            this.$v.manualLoyerForm.$touch();

            if (this.$v.manualLoyerForm.$invalid) {
                return;
            }

            const data = new FormData();

            data.append('bail_id', this.manualLoyerForm.bail_id);
            data.append('local_id',this.manualLoyerForm.local_id);
            data.append('details',JSON.stringify(this.localSelected));
            data.append('montantLoyer', this.manualLoyerForm.montantLoyer);
            data.append('moisLoyer', this.manualLoyerForm.moisLoyer.split('/')[1]+'-'+this.manualLoyerForm.moisLoyer.split('/')[0]);

            axios.post("/operations/ajoutPaiementLoyerManuel", data,  {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                if(response.data.code==0){

                Vue.swal.fire(
                    'Success',
                    'Paiement loyer enregistré avec succés',
                    'success'
                ).then((result) => {
                    this.getOperations();
                    this.$refs.closePopup.click();
                    this.reserFormAdd();
                });


                }else{
                     Vue.swal.fire(
                      'error!',
                      response.data.message,
                      'error'
                    )
                }
                this.manualIsSubmitted = false;
            });
        },
        createPaiement(){

            let has_new_line = false;

            for(var i=0; i<this.paiements.length; i++){

                if(isNaN(this.paiements[i].paiementMontant) || this.paiements[i].paiementMontant==''){
                    Vue.swal.fire(
                      'Erreur montant!',
                      'Veuillez saisir un montant valide',
                      'error'
                    );
                    return false;
                }

                if(!this.paiements[i].validate){
                    has_new_line = true;
                }
            }
            if(!has_new_line){
                Vue.swal.fire(
                      'Paiement!',
                      'Veuillez ajouter un paiement',
                      'error'
                    );
                return false;
            }
            const data = new FormData();

            data.append('id_bail', this.paiement_bail_id);
            data.append('id_loyer', this.id_loyer);
            data.append('id_locataire', this.infoLocataire.id);
            data.append('avoir', this.avoir);
            data.append('montant_loyer', this.infoLocataire.loyer_hors_charge);
            data.append('paiements', JSON.stringify(this.paiements));

            axios.post("/operations/ajout", data,  {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                if(response.data.code==0){

                Vue.swal.fire(
                    'Success',
                    'Paiement enregistré avec succés',
                    'success'
                ).then((result) => {
                    this.getOperations();
                    this.$refs.closePopup.click();
                    this.reset();
                });




                }else{
                     Vue.swal.fire(
                      'error!',
                      response.data.message,
                      'error'
                    )
                }
                this.isSubmitted = false;

            });
        },
        addPaimentAPI(){
            if(this.addPaimentForm.useSolde && this.calculMntRestant(this.infoLocataire.loyer_hors_charge, this.infoLocataire.locat_avoir) <= 0){
                this.addPaimentForm.paiementMnt = '0';
                this.addPaimentForm.paiementType = 'Utilisation solde';
            }

            this.isPaymentSubmitted = true;


            // stop here if form is invalid
            this.$v.addPaimentForm.$touch();

            if (this.$v.addPaimentForm.$invalid) {
                console.log(">> failed isSubmitted")

                return;
            }

            this.paymentRun = true;

            const data = new FormData();
            const date = new Date().toISOString().slice(0, 10);
            const time = new Date().toISOString().slice(11, 19);


            const paiementClone = this.paiements;
            paiementClone.push({
                paiementMontant: this.addPaimentForm.paiementMnt,
                paiementDate: `${date} ${time}`,
                paiementType: this.addPaimentForm.paiementType,
                validate: false
              });

            data.append('date_paiement', `${date} ${time}`);

            data.append('id_bail', this.paiement_bail_id);
            data.append('id_loyer', this.id_loyer);
            data.append('id_locataire', this.infoLocataire.id);
            data.append('paiements', JSON.stringify(paiementClone));
            data.append('avoir', this.avoir);
            data.append('montant_payer', this.addPaimentForm.paiementMnt);
            data.append('montant_loyer', this.infoLocataire.loyer_hors_charge.replace(/\s+/g, ''));
            data.append('montant_payer_en_lettre', this.numberToLetter(this.addPaimentForm.paiementMnt.replace(/\s+/g, '')));

            data.append('useSolde', this.addPaimentForm.useSolde);
            data.append('mntApayer', this.mntRestantAPayer);
            data.append('file[]', this.addPaimentForm.attachmentsFiles);

            for (let i = 0; i < this.addPaimentForm.attachmentsFiles.length; i++) {
                data.append('files' + i, this.addPaimentForm.attachmentsFiles[i]);
            }
            data.append('TotalFiles', this.addPaimentForm.attachmentsFiles.length);

            axios.post("/operations/ajoutPaiement", data,  {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                if(response.data.code==0){
                    this.isPaymentSubmitted = false;
                    this.paymentRun = false;
                    this.showFormPaiement(false);
                    Vue.swal.fire(
                        'Success',
                        'Paiement enregistré avec succés',
                        'success'
                    ).then((result) => {
                        this.getOperations(this.currentPage);
                        this.paiements = JSON.parse(response.data.paiementDetails);
                        this.responseSolde = JSON.parse(response.data.resultPaiementSolde);
                        //this.infoLocataire.locat_avoir = this.responseSolde.solde_apres;
                        this.addPaimentForm.useSolde = false;
                        this.addPaimentForm.paiementMnt = '';
                        this.addPaimentForm.paiementType = '';


                       // this.$refs.closePopup.click();
                       // this.reset();
                    });

                }else{
                     Vue.swal.fire(
                      'error!',
                      response.data.message,
                      'error'
                    )
                }
                this.isSubmitted = false;

            });
        },
        async checkCanPayLoyer(oper) {
            try {
              const response = await axios.get(`/operations/paiement-loyers/checkCanPayMoisLoyer/${oper.paiement_bail_id}/${oper.loyer_y_m}`);
              console.log(">>>ATPS cour", response.data.isPayable);

              const resp = response.data;
              if (resp.code === 0) {
                console.log("WW>>", resp.isPayable);
                return resp.isPayable;
              }

            } catch (error) {
              console.error("Erreur lors de la vérification du paiement :", error);
              return false; // Retourne false en cas d'erreur
            }
          },
        toggleDropdown() {
          this.showDropdown = !this.showDropdown;
        },
        closeDropdown() {
          // Délai pour permettre la sélection avant de fermer
          setTimeout(() => {
            this.showDropdown = false;
          }, 200);
        },
        selectLocataire(id) {
          this.selectedLocataireId = id;
          this.searchQuery = this.listLocataireTab.find((s) => s.locat_id === id).locat_nom;
          this.showDropdown = false;
          this.getOperations();
        },
        showQuittance(urlFile){

            EventBus.$emit('VIEW_FILE', {
                pathFile: urlFile
            });
        },
        selectLocatairePaiement(locataire){
            this.selectedLocataireId = locataire.id;
            this.infoLocataire.locataireChoosen = locataire.locat_nom+' '+locataire.locat_prenom;
            // charger la liste des locations actif pour le locataire
            this.isLodingLocalActif = true;
            axios.get("/operations/loyers_actif/id_locataire="+this.selectedLocataireId).then(responses => {
               console.log(responses);
               this.localActif = responses.data;
               this.isLodingLocalActif = false;

            }).catch(errors => {

            // react on errors.

            })


        },
        setInfosLocation(local){

            for (let obj of this.findBailByLocal) {
                if (obj.hasOwnProperty(local.local_id)) {
                  this.manualLoyerForm.bail_id = obj[local.local_id];
                }
            }
            this.manualLoyerForm.montantLoyer = local.local_prix_loyer;
            this.manualLoyerForm.local_id = local.local_id;
            this.localSelected = local;

        },
        close(){
            this.isNewPaiment = false;
        },
        handleDateChange(newDate) {
          console.log("Nouvelle date sélectionnée :", newDate);
          if(this.infoLocataire.date_du && this.infoLocataire.date_au){
            this.months =  this.getMonthsBetweenDates(this.infoLocataire.date_du, this.infoLocataire.date_au);
            console.log('list mois', this.months);
            this.reste = this.infoLocataire.loyer_hors_charge * this.months.length;
          }

        },
        getLocalLoueByLocataire(id_locataire){
            console.log("param", id_locataire)
            axios.get("/bail/find_local_loue/"+id_locataire).then(responses => {
               console.log(responses);
               if(responses.data.code == 0){
                    this.localActif = responses.data;
                    this.findBailByLocal = responses.data.findBailByLocal;
               }else{
                    Vue.swal.fire(
                      'Erreur recuperation locataire!',
                      '',
                      'error'
                    );
               }



            }).catch(errors => {

            // react on errors.

            })
        },
        onLocataireChoisi(locataire) {
          console.log("Locataire sélectionné :", locataire);
          this.manualLoyerForm.locataire = locataire;
          this.getLocalLoueByLocataire(locataire['locat_id'])
        },
        deletePaiement(oper){
             Vue.swal.fire({
              title:"Suppression Paiement ",
              text: "Attention!!! cette opération est irréversible.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#3085d6',
              confirmButtonText: 'Oui, supprimer!'
            }).then((result) => {
            if (result.isConfirmed) {

                axios.delete('/operations/paiements/delete/'+oper.identifiant).then(response => {
                    console.log(response);
                    if(response.data.code==0){
                         Vue.swal.fire(
                          'Suppression',
                          'Paiement supprimé avec succés',
                          'success'
                        );
                        this.getOperations();
                    }else{
                        Vue.swal.fire(
                          'Suppression',
                          'Une erreure est survenue!',
                          'error'
                        );
                    }
                });
              }
            })
        }

    },
    computed: {
        filteredLocataires() {
            return this.listLocataireTab.filter((locataire) =>
                locataire.locat_nom.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },
    },
    mounted() {
        this.listLocataireTab=this.listLocataire;
        this.getOperations();
        console.log("Liste locataire", this.listLocataire)


    }
}
</script>
<style scoped>
 .tr-data td {
    cursor: pointer;
}
.custom-select {
  position: relative;
  width: 250px;
  height: auto;
  background: #fff !important;
  border: 0 !important;
  box-shadow: none !important;
  padding-right: 0 !important;
  padding-top: 3px !important;
  padding-bottom: 3px !important;
}

.custom-select input {
  box-sizing: border-box;
}


.disable-input{
    pointer-events: none;
    opacity: .5;
}
.custom-select .dropdown {
  position: absolute;
  width: 97%;
  border: 1px solid #ccc;
  max-height: 150px;
  overflow-y: auto;
  background-color: white;
  z-index: 1000;
}

.custom-select ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.custom-select li {
  padding: 5px;
  cursor: pointer;
}

.custom-select li:hover {
  background-color: #eee;
}

/*Loyer paiement*/
.formAddPayment{
    width: 600px;
    background: #e1e1e1;
    padding: 15px;
    border-radius: 15px;
    position: absolute;
    z-index: 9999;
    left: calc((100% - 600px)/2);
    top: 70px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.43);
    opacity: 0;
    transform: translateX(50px);
    transition: opacity 0.5s ease-out, transform 0.5s ease-out;
}
.formAddPayment.actifForm{
    opacity: 1;
    transform: translateX(0);
}
.formAddPayment label{
    font-weight: normal;
}
.formAddPayment h3{
    font-size: 25px;
    font-weight: bold;
    text-transform: uppercase;
    text-align: center;
    padding-top: 15px;
}
.formAddPayment input[type='text'],
.formAddPayment input[type='number'],
.formAddPayment select{
  height: 3rem !important;
  font-size: 25px;
  border: 2px solid #6c6c6d;
  border-radius: 8px;
  padding-top: 8px !important;
}
 .containerLoyer {
    background: white;
    padding: 20px;
    font-family: "Open Sans", Helvetica, Arial, sans-serif;
    position: relative;
}
.containerLoyer h2{
  font-size: 1.2rem;
  font-weight: 600;
  color: #000;

}
.containerLoyer p{
    margin-bottom: 5px;
}
.disabledRem{
    opacity: .3;
}
.containerLoyer .card {
    margin-bottom: 20px;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background: #fff;
}
.containerLoyer table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;

}
.containerLoyer th, .containerLoyer td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}
.containerLoyer .progress-bar {
    width: 100%;
    background: #ddd;
    border-radius: 5px;
    overflow: hidden;
    margin: 10px 0;
}
.containerLoyer .progressLine {
    height: 20px;
    background: #4caf50;
}
.actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-bottom: 15px;
}
.containerLoyer button:disabled {
    background: #ccc;
}
.disabledDiv{
    opacity: 0.2;
    pointer-events: none;
}
.actionPaiement{
    display: inline-flex;
    justify-content: center;
    width: 100%;
    padding-top: 15px;
    padding-bottom: 15px;
    gap: 15px;
}
.actionPaiement button{
    font-size: 22px;
    padding-top: 10px;
    border-radius: 10px;
}
</style>

