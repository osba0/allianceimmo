<template>
    <div>
        <div class="d-flex justify-content-between mb-3">
            <div class="d-flex justify-content-between mb-3">
                <div class="d-flex justify-content-between align-items-center gap-15">
                    <div class="d-flex align-items-center">
                        <label class="text-nowrap mr-2 mb-0">Nbre de ligne par Page</label>
                        <select class="form-control form-control-sm" v-model="paginate">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                        </select>
                    </div>
                    <!-- Filtres -->
                    <div class="d-flex align-items-end gap-15">
                        <div style="width: 350px">
                             <v-select v-model="proprioSelected" @input="onInputSelectProprio" placeholder="Filtrer par propriétaire" :options="listProprio"  @option:selected="onProprioChoisi" label="item_proprio"></v-select>
                        </div>
                    </div>
                </div>
            </div>

        </div>

         <div class="table-responsive">
            <table class="table table-hover table-striped">
              <thead class="bg-white">
                <tr>

                    <th>Compte</th>
                    <th>Type</th>
                    <th>Montant à encaisser</th>
                     <th>Status</th>

                    <th>Date</th>
                    <th>Initié par</th>
                    <th class="text-right">Action</th>
                </tr>
                <tr>
                    <th colspan="9" class="position-relative p-0">
                        <div class="loader-line" :class="[isLoading?'d-block':'d-none']"></div>
                    </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="operation in operations.data" :key="operation.identifiant">
                    <td class="align-middle">
                        {{operation.proprio_nom}} {{operation.proprio_prenom}}
                    </td>

                    <td class="align-middle">{{ operation.type }}</td>
                    <td class="align-middle">{{ helper_separator_amount(operation.montant) }}</td>
                     <td class="align-middle">{{ operation.oper_statut }}</td>

                    <td class="align-middle">{{ operation.date_creation }}</td>
                    <td class="align-middle">
                        {{ operation.user }}
                    </td>

                    <td class="text-right align-middle">
                        <button class="btn btn-sm btn-success"  v-on:click="operSelected(operation)"  data-toggle="modal" data-target="#validationOperation"><i class="fa fa-check"></i> Encaisser</button>
                        <button class="btn btn-sm btn-danger"  v-on:click="operSelected(operation)"  data-toggle="modal" data-target="#validationOperation"><i class="fa fa-times"></i> Rejeter</button>
                    </td>
                </tr>
              </tbody>
            </table>
        </div>
        <div class="d-flex mt-4 justify-content-center">
            <pagination
                :data="operations"
                :limit=10
                @pagination-change-page="getEncaissement"
            ></pagination>
        </div>

         <!-- Modal -->
        <div class="modal fade" id="validationOperation" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="validationOperation" aria-hidden="true">
            
            <div class="modal-dialog modal-md position-relative" role="document">
                <div class="loader-line" :class="[isLoading?'d-block':'d-none']"></div>
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-uppercase" v-show="!editmode"><strong><span class="text-primary"><u>Validation</u></span> Encaissement</strong></h5>
                    <h5 class="modal-title" v-show="editmode"><strong><span class="text-primary"><u>Editer</u></span> Versement</strong></h5>
                    <button type="button" class="close" ref="closePopup" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 

                <form @submit.prevent="validerPaiement()">
                    <div class="modal-body">

                       <div class='formAddPayment'>

                            <div>
                                <div class="form-group mb-3">
                                <label>Montant à payer:</label>
                                   <input type="number" class="form-control inputTextStyled" :value="form.montantPayer" disabled />
                                </div>

                            </div>
                             <div>
                                <div class="form-group mb-3">
                                    <label>Montant remis:</label>
                                    <input type="number" class="form-control inputTextStyled" v-model.number="form.montantRemis" required />
                                </div>

                            </div>
                             <div>
                                <div class="form-group mb-3">
                                    <label>Montant à rendre:</label>
                                    <input type="number" class="form-control inputTextStyled" :value="montantARendre" disabled />
                                </div>

                            </div>

                        </div>

                    </div>
                    
                    <div class="modal-footer justify-content-center">
                        <button type="submit" class="d-flex align-items-center btn btn-success text-nowrap" :disabled="paymentRun || form.montantRemis < form.montantPayer" style="width: auto; gap: 8px"><span v-if="paymentRun" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <span v-else><i class="fa fa-check"></i></span><span>Valider l'encaissement</span></button>

                        <button type="button" class="btn btn-secondary " @click="reset()" data-dismiss="modal">Annuler</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>

        <modalDocument/>
        <modalInfo/>
    </div>
</template>

<script>
import DatePicker from 'vue2-datepicker';
import VueCountryDropdown from 'vue-country-dropdown';
import 'vue2-datepicker/index.css';
import modalDocument from '../../components/modal/document.vue';
import modalInfo from '../../components/modal/viewInfos.vue';
import { EventBus } from "../../event-bus"; 
import { required, email, minLength, between } from 'vuelidate/lib/validators';


// Pdf
import { PdfMakeWrapper, Table, QR, Img} from 'pdfmake-wrapper';

import { ITable } from 'pdfmake-wrapper/lib/interfaces'; 

import pdfFonts from "pdfmake/build/vfs_fonts";

export default {
    name: "Encaissement",
    props: ["listProprio", "listLocataires"],
    components: { DatePicker, VueCountryDropdown, modalDocument, modalInfo },
    data () {
        return {
            paymentRun: false,
            hasData: false,
            editmode: false,
            isLoading: false,
            isSubmitted: false,
            operations : {},
            paiements: [],
            paginate: 5,
            editmode: false,
            form: {
                id : '',
                montantPayer: null,
                montantRemis: 0,
                montantARendre: 0,
                proprio : '',
                bien: '',
                montant: '',
                decharge: null,
                type: '',
                date: '',
                note: '',
                moyen_paiement: ''
               
            },
          //  biens: [],
            locals: [],
            attachmentsDecharge: [],
            editFile: [],
            proprioSelected: null,
            proprioID: '',
            biensFiltres: []
        }
    },
    validations: {
        form : {
            proprio: { required },
            montant: { required },
            type: { required }

        },
        
    },
    watch: {
       paginate: function(){
            this.getEncaissement();
       }
      
    },
     computed: {
        montantARendre() {
        const remis = parseFloat(this.form.montantRemis || 0);
        const aPayer = parseFloat(this.form.montantPayer || 0);
        return Math.max(0, remis - aPayer);
          }
    },
    methods: {
        operSelected(oper){
            this.form.montantPayer = oper.montant;
            this.form.id = oper.identifiant;
        },
        getEncaissement(page=1){
           this.isLoading = true;
            const params = {};
            if (this.proprioID) params.proprioID = this.proprioID;
            axios.get("/operations/encaissements/all?paginate="+this.paginate+'&page=' + page, {params}).then(responses => {
               console.log(responses);
               this.operations = responses.data;
                if(this.operations.data.length == 0){
                    this.hasData = false;
                  }
                  else{
                     this.hasData = true;
                  }

               this.isLoading = false;
            }).catch(errors => { 

            // react on errors.

            })
        },
        newModal(){

        },
        reset(){
            this.form.id = '';
            this.form.proprio = '';
            this.form.bien = '',
            this.form.montant = '';
            this.form.decharge = null;
            this.form.type = '';
            this.form.date = '';
            this.form.note = '';
            this.form.moyen_paiement = ''
        },
        closeModal(val){

        },
        handleFileUpload(){
            this.attachmentsDecharge = [];
            for(var i=0; i<this.$refs.attachmentsDecharge.files.length;i++){
                this.attachmentsDecharge.push(this.$refs.attachmentsDecharge.files[i])
            }
        },
        onInputSelectProprio(value) {
          if (!value) {
            this.proprioSelected = null;
            this.proprioID = '';
            this.getEncaissement(); // 💡 appel de ton action de réinitialisation
          }
        },
        onProprioChoisi(proprio){
          this.proprioSelected = proprio;
          this.proprioID = proprio.proprio_id;
          this.getVersements();
        },
        showDocument(file){
             EventBus.$emit('VIEW_DOCUMENT', {
                path: file,
                title: 'Fichier'
            });
        },
        showInfo(note){
            EventBus.$emit('VIEW_TEXT', {
                texte: note,
                title: 'Info'
            });
        },
        validerPaiement() {
          if (this.form.montantRemis >= this.form.montantPayer) {
            // Appel API ou confirmation ici
             Vue.swal.fire({
              title:"Validation Paiement ",
              text: "Confirmer l'encaissement.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: 'rgb(20, 164, 29)',
              cancelButtonColor: '#3085d6',
              confirmButtonText: 'Oui, valider!'
            }).then((result) => {
            if (result.isConfirmed) {

                axios.get('/operations/encaissements/validation/'+this.form.id).then(response => {
                    console.log(response);
                    if(response.data.code==0){
                         Vue.swal.fire(
                          'Validation',
                          'Encaisser avec succés',
                          'success'
                        );
                        this.$refs.closePopup.click();
                        this.form.montantRemis = 0;
                        this.form.montantARendre = 0;
                        this.getEncaissement();
                    }else{
                        Vue.swal.fire(
                          'Validation',
                          'Une erreure est survenue!',
                          'error'
                        );
                    }
                });
              }
            })
          } else {
            this.$swal.fire('Erreur', 'Montant remis insuffisant', 'error');
          }
        }

    },
    mounted() {
        console.log(this.listProprio) ;
        this.listProprio.map(function (x){
          return x.item_proprio = (x.proprio_nom + ' ' + x.proprio_prenom + ' (' +x.proprio_id +')');
        });  

        this.getEncaissement();
    }
}
</script>
