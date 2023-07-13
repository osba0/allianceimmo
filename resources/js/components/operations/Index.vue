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
                    <th>Echéance</th>
                    <th>Paiement</th>
                    <th class="text-right">Action</th>
                </tr>
                <tr>
                    <th colspan="9" class="position-relative p-0">
                        <div class="loader-line" :class="[isLoadingTab?'d-block':'d-none']"></div>
                    </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="oper in operations.data" :key="oper.identifiant" >
                    <td class="align-middle">
                        {{oper.locat_nom}} {{oper.locat_prenom}}
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
                    <td class="align-middle">{{ oper.bail_montant_ht }}</td>
                    <td class="align-middle">{{ oper.paiement_echeance | formatDateFR }}</td>
                    <td class="align-middle">
                        <label class="" :class="[oper.paiement_etat==3?'text-success':'', oper.paiement_etat==2?'text-warning':'', oper.paiement_etat==0?'text-danger':'']">{{ etatPaiement[oper.paiement_etat] }}</label>
                    </td>
                    <td class="text-right align-middle">
                        <button class="btn btn-success position-relative mr-3" data-toggle="modal" data-target="#paiement" v-on:click="editLoyer(oper)" ><i class="fa fa-money-bill"></i><span class="badge border border-warning badge-light position-absolute total-right-corner">{{ Array.isArray(oper.paiements)? oper.paiements.length : 0}}</span></button>
                        <button class="btn btn-danger" @click="deleteProprio(oper)"><i class="fa fa-trash"></i></button>
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
                    <h5 class="modal-title text-uppercase"><strong><span class="text-primary"><u>Editer</u></span> un loyer</strong></h5>
                  
                    <button type="button" class="close" ref="closePopup" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 

                <form @submit.prevent="createPaiement()">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="d-block text-uppercase text-info border-bottom titleform border-info">Infos Locataire</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                
                                <div class="form-group">
                                    <label>Nom & Prénom</label>
                                    <input disabled  type="text" v-model="infoLocataire.locataire" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="form-group w-49">
                                        <label>Loyer hors charges</label>
                                        <input disabled  type="text" v-model="infoLocataire.loyer_hors_charge" class="form-control"/>
                                    </div>
                                     <div class="form-group w-49">
                                        <label>Charges</label>
                                        <input disabled  type="text" v-model="infoLocataire.charges" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="form-group w-50 mr-2">
                                        <label>Période du</label>
                                        <date-picker disabled v-model="infoLocataire.date_du" class="w-100"  required valueType="YYYY-MM-DD"  :disabled-date="disabledFutureDate" input-class="form-control w-100 mr-2" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>
                                    </div>
                                    <div class="form-group w-50 ml-2">
                                        <label>au</label>
                                        <date-picker disabled v-model="infoLocataire.date_au" class="w-100"  required valueType="YYYY-MM-DD"  :disabled-date="disabledFutureDate" input-class="form-control w-100 mr-2" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                               <div class="d-flex align-items-center">
                                    <div class="form-group w-50 mr-2">
                                        <label>Loyer</label>
                                         <input disabled  type="text" v-model="infoLocataire.loyer" class="form-control"/>
                                    </div>
                                    <div class="form-group w-50 ml-2">
                                        <label>Echéance</label>
                                        <date-picker disabled v-model="infoLocataire.echeance" class="w-100"  required valueType="YYYY-MM-DD"  :disabled-date="disabledFutureDate" input-class="form-control w-100 mr-2" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 ">
                                <label>Local</label> 
                                <ul class="p-0 d-flex list-unstyled" v-if="infoLocataire.local.length > 0">
                                    <li v-for="(local, index) in infoLocataire.local" class="border-left pl-2 mr-2"> 
                                    {{ local.local_type_local }} <br/>
                                    {{ local.local_type_location }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                         <div class="row">
                           
                            <div class="col-md-12">
                                <label class="d-block text-uppercase text-success border-bottom titleform border-success">Paiements</label>
                                 <div class="form-group">
                                    <label>Indiquez les différents paiements reçus.</label> 
                                    <div class="w-100 max-height-200 linePaiement">
                                        <div v-for="(paiment, index) in paiements" :key="index" class="pr-5 d-flex align-items-center position-relative mb-2">
                                                <input
                                                v-model="paiment.paiementMontant"
                                                placeholder="Saisir un montant"
                                                class="form-control"
                                                :disabled="paiment.validate || paiment.is_avoir"
                                                />
                                                <template v-if="paiment.is_avoir && etatLoyer!=3">
                                                    <span class="text-success font-weight-bold px-3 border ml-2">Avoir</span>
                                                </template>
                                                <template v-else>
                                                <span class="px-3 nowrap">FCFA reçu le</span>
                                                <date-picker v-model="paiment.paiementDate" class="w-100"  required valueType="YYYY-MM-DD" :disabled="paiment.validate" :disabled-date="disabledFutureDate" input-class="form-control w-100 mr-2" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>
                                                <button
                                                    type="button"
                                                    class="btn btn-danger position-absolute right-0"
                                                    @click="remove(index)"
                                                    v-show="index != 0 && !paiment.validate"
                                                  >
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="pointer-events-none btn btn-success position-absolute right-0"
                                                    v-show="paiment.validate && !paiment.is_avoir"
                                                  >
                                                    <i class="fa fa-check"></i>
                                                </button>
                                                <button v-if="paiment.is_avoir && etatLoyer==3" class="pointer-events-none btn btn-success position-absolute right-0">A</button>
                                                </template>
                                                
                                        </div>
                                    </div>
                                    <div class="w-100 d-flex justify-content-between pt-5 pr-5">
                                        <div class="d-flex align-items-center label-arrow" v-if="etatLoyer!=3">

                                            <label class="font-weight-bold h2 bg-success mr-3 px-3 picto-item"  aria-label="Avoir">{{ abs(helper_separator_amount(avoir) )}} </label>
                                            
                                            <label class="font-weight-bold h2 bg-danger mr-3 px-3 picto-item" aria-label="Reste à Payer">{{ (isNaN(reste) || reste=='') && reste!= 0 ? '-':helper_separator_amount(reste) }} </label>

                                            <label class="font-weight-bold h2 bg-info mr-3 px-3 picto-item" aria-label="Montant Loyer">{{ helper_separator_amount(infoLocataire.loyer_hors_charge) }}</label>
                                          
                                          
                                          <span class="nowrap flex-1">FCFA TOTAL</span>
                                        </div>
                                        <label class="text-success h2 text-uppercase px-3 py-2 border border-success rounded-lg" v-if="etatLoyer==3">
                                            <i class="fa fa-check"></i> Payé</label>
                                        <button
                                        type="button"
                                        class="btn btn-primary"
                                        :disabled="reste == 0"
                                        @click="addMore()"
                                        v-else
                                      >
                                       <i class="fa fa-plus"></i> Ajouter un paiement
                                        </button>
                                    </div>
                                      
                                    
                                </div>
                            </div>
                        </div>

                          
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="submit" class="btn btn-success" v-show="etatLoyer!=3">Enregister</button>
                        <button type="button" class="btn btn-secondary " @click="reset()" data-dismiss="modal">Fermer</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>
        
    </div>
</template>

<script>
import DatePicker from 'vue2-datepicker';
import VueCountryDropdown from 'vue-country-dropdown';
import 'vue2-datepicker/index.css';
import { EventBus } from "../../event-bus"; 
import { required, email, minLength, between } from 'vuelidate/lib/validators';


// Pdf
import { PdfMakeWrapper, Table, QR, Img} from 'pdfmake-wrapper';

import { ITable } from 'pdfmake-wrapper/lib/interfaces'; 

import pdfFonts from "pdfmake/build/vfs_fonts";

export default {
    name: "Operations",
    props: ["listProprio", "listLocataire", "etatPaiement"],
    components: { DatePicker, VueCountryDropdown },
    data () {
        return {
            editmode: false,
            isLoading: false,
            isSubmitted: false,
            isLoadingTab: false,
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
                locat_avoir: ''
            },
            id_loyer: "",
            paiement_bail_id: "",
            etatLoyer: "",
            paiements: [],
            paginate: 5,
            currentLoyer: null
        }
    },
    validations: {
        form : {
            proprio:     { required }
            

        },
        
    },
    watch: {
       paiements: {
             handler: function(newValue) {
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
       paginate: function(){
            this.getOperations();
       }
      
    },
    methods: {
    
       getOperations(page=1){

           this.isLoadingTab = true;
            axios.get("/operations/paiement_loyer?paginate="+this.paginate+'&page=' + page).then(responses => {
               console.log(responses);
               this.operations = responses.data;
               this.isLoadingTab = false;
            }).catch(errors => { 

            // react on errors.

            })
       },
       newModal(){

       },
       reset(){
        this.paiements = [];
        this.paiements['paiementMontant'] = 0;
        this.paiements['paiementDate'] = new Date().toISOString().slice(0,10);
        this.paiements['validate'] = false;
        this.paiements['is_avoir'] = false;
        this.currentLoyer = null;

       },
       editLoyer(oper){

        this.currentLoyer = oper;
        this.infoLocataire.id                = oper.bail_locataire;
        this.infoLocataire.locataire         = oper.locat_nom+' '+oper.locat_prenom;
        this.infoLocataire.loyer_hors_charge = oper.bail_montant_ht;
        this.infoLocataire.echeance          = oper.paiement_echeance;
        this.infoLocataire.date_du           = oper.periode_du;
        this.infoLocataire.date_au           = oper.periode_au;
        this.infoLocataire.local             = oper.locaux;
        this.infoLocataire.proprio_id        = oper.proprio_id;
        this.locat_avoir                     = oper.locat_avoir;
        this.id_loyer = oper.identifiant;
        this.paiement_bail_id = oper.paiement_bail_id;
        this.etatLoyer = oper.paiement_etat;
        if(oper.paiements){
            this.paiements = oper.paiements;
        }else{
            this.paiements = [];
        }
        if(parseInt(this.locat_avoir) > 0 && this.etatLoyer!=3){   
        this.paiements.push({
                paiementMontant: this.locat_avoir,
                paiementDate: new Date().toISOString().slice(0,10),
                validate: false,
                is_avoir: true
              });
        }
        
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

            
        }

    },
    mounted() {
        this.getOperations();
        
        
    }
}
</script>
