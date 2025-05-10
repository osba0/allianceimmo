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
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addNew" v-on:click="newModal" ><i class="fa fa-plus"></i> Nouveau Versement</button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
              <thead class="bg-white">
                <tr>
                    <th>Identifiant</th>
                    <th>Propriétaires</th>
                    <th>Bien </th>
                    <th>Montant</th>
                    <th>Type</th>
                    <th>Moyen paiement</th>
                    <th>User</th>
                    <th>Date</th>
                    <th class="text-right">Action</th>
                </tr>
                <tr>
                    <th colspan="9" class="position-relative p-0">
                        <div class="loader-line" :class="[isLoading?'d-block':'d-none']"></div>
                    </th>
                </tr>
              </thead>
              <tbody>
                <template v-if="!hasData">
                    <tr><td colspan="8" class="bg-white text-center">{{isLoading?'Chargement en cours...':'Aucun versement!'}}</td></tr>
                </template>
                <tr v-for="v in versements.data" :key="v.id">
                    <td class="align-middle"><span class="badge badge-success">{{ v.identifiant }}</span></td>
                    <td class="align-middle">{{ v.proprio_nom }} {{ v.proprio_prenom }}</td>
                    <td class="align-middle">{{ v.bien_nom || '' }}, n°{{ v.bien_numero || '-' }} {{ v.bien_adresse || '-' }},  {{ v.bien_ville  || '-'}} ({{ v.bien_pays }})</td>
                    <td class="align-middle">{{ helper_separator_amount(v.montant) }}</td>
                    <td class="align-middle text-uppercase">{{ v.type }}</td>
                    <td class="align-middle text-uppercase"> {{ v.moyen_paiement }}</td>
                     <td class="align-middle">{{ v.user }}</td>
                    <td class="align-middle">{{ v.date }}</td>

                    <td class="text-right align-middle">
                        <button  title="Fichier" v-if="v.fichier.length > 0" class="btn btn-info mr-2 cursor-pointer" data-toggle="modal" data-target="#modalFichier" v-on:click="showDocument('assets/versements/'+v.fichier[0])">
                                <i class="fa fa-file-pdf"></i>
                        </button>
                        <button  v-if="v.note != ''" class="btn btn-primary mr-2 cursor-pointer" v-on:click="showInfo(v.note)">
                            <i class="fa fa-info"></i>
                        </button>
                        <button class="btn btn-danger" v-on:click="deleteVersement(v)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
              </tbody>
            </table>
        </div>
        <div class="d-flex mt-4 justify-content-center">
            <pagination
                :data="versements"
                :limit=10
                @pagination-change-page="getVersements"
            ></pagination>
        </div>

         <!-- Modal -->
        <div class="modal fade" id="addNew" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="addNew" aria-hidden="true">
            
            <div class="modal-dialog modal-xl position-relative" role="document">
                <div class="loader-line" :class="[isLoading?'d-block':'d-none']"></div>
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-uppercase" v-show="!editmode"><strong><span class="text-primary"><u>Nouveau</u></span> Versement</strong></h5>
                    <h5 class="modal-title" v-show="editmode"><strong><span class="text-primary"><u>Editer</u></span> Versement</strong></h5>
                    <button type="button" class="close" ref="closePopup" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 

                <form @submit.prevent="createVersements()">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="d-block text-uppercase text-info border-bottom titleform border-info">Propriétaire Lié</label>

                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Choisir un propriétaire <span class="text-danger">*</span></label>
                                     <v-select :class="{ 'border-danger': isSubmitted && !$v.form.proprio.required }" v-model="form.proprio" @option:selected="onSelectBien"  :options="listProprio" :reduce="(option) => option" label="item_proprio"></v-select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Bien concerné (facultatif)</label>
                                     <v-select v-model="form.bien" :options="biensFiltres" :reduce="(option) => option" label="item_bien"></v-select>

                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="d-block text-uppercase text-success border-bottom titleform border-success">Versement</label>
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-md-6">
                                <label class="pt-2">Type versement <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center">
                                  <select class="form-control" v-model="form.type" :class="{ 'border-danger': isSubmitted && !$v.form.type }">
                                        <option value="">Choisir</option>
                                        <option value="achat">Achat</option>
                                        <option value="entretien">Entretien</option>
                                        <option value="investissement">Investissement</option>
                                        <option value="famille">Famille</option>
                                        <option value="autre">Autre</option>
                                  </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="pt-2">Montant <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center">
                                    <input v-model="form.montant" type="text" :class="{ 'border-danger': isSubmitted && !$v.form.montant.required }" class="form-control">
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-6">
                                <label class="pt-2">Date versement</label>
                                <div class="d-flex align-items-center">
                                    <date-picker v-model="form.date" class="w-100"  required valueType="YYYY-MM-DD" input-class="form-control w-100" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="pt-2">Moyen de paiement</label>
                                <select class="form-control" v-model="form.moyen_paiement">
                                        <option value="espéce">Espéce</option>
                                        <option value="virement">Virement</option>
                                        <option value="wallet">Wallet</option>
                                        <option value="autre">Autre</option>
                                  </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="pt-2">Description</label>
                                <div class="d-flex align-items-center">
                                    <textarea  v-model="form.note" class="form-control" placeholder="Renseigner plus de détails sur le versement">

                                    </textarea>

                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pt-2 mb-0">Joindre un fichier</label>
                                    <input name="file"  type="file"  ref="attachmentsDecharge"
                                        class="form-control border-0 pl-0" v-on:change="handleFileUpload()">
                                </div>
                            </div>
                        </div> 
                       
                    </div>
                    
                    <div class="modal-footer justify-content-center">
                        <button v-show="editmode" type="submit" class="btn btn-success">Enregister</button>
                        <button v-show="editmode" type="button" class="btn btn-warning"  data-dismiss="modal" @click="reset()">Annuler</button>
                        <button v-show="!editmode" type="submit" class="btn btn-success">Valider</button>
                        <button  v-show="!editmode" type="button" class="btn btn-info btn" @click="reset()">Réinitialiser</button>
                        <button  v-show="!editmode" type="button" class="btn btn-secondary " @click="reset()" data-dismiss="modal">Annuler</button>
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
    name: "VersementProprietaire",
    props: ["listProprio", "biens"],
    components: { DatePicker, VueCountryDropdown, modalDocument, modalInfo },
    data () {
        return {
            hasData: false,
            editmode: false,
            isLoading: false,
            isSubmitted: false,
            versements : {},
            paiements: [],
            paginate: 5,
            editmode: false,
            form: {
                id : '',
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
            this.getVersements();
       }
      
    },
    methods: {
        getVersements(page=1){
           this.isLoading = true;
            const params = {};
            if (this.proprioID) params.proprioID = this.proprioID;
            axios.get("/operations/versements/all?paginate="+this.paginate+'&page=' + page, {params}).then(responses => {
               console.log(responses);
               this.versements = responses.data;
                if(this.versements.data.length == 0){
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
        editVersement(oper){

        },
        handleFileUpload(){
            this.attachmentsDecharge = [];
            for(var i=0; i<this.$refs.attachmentsDecharge.files.length;i++){
                this.attachmentsDecharge.push(this.$refs.attachmentsDecharge.files[i])
            }
        },
        createVersements(){
            this.isSubmitted = true;

            // stop here if form is invalid
            this.$v.form.$touch();

            if (this.$v.form.$invalid) {
                return;
            }          

            const data = new FormData();
            data.append('proprio', this.form.proprio.proprio_id);
            data.append('bien', this.form.bien.bien_id);
            data.append('montant', this.form.montant);
            data.append('proprio_email', this.form.proprio.proprio_email);
            data.append('proprio_nom', this.form.proprio.proprio_nom);
            data.append('proprio_prenom', this.form.proprio.proprio_prenom);

            data.append('type', this.form.type);
            data.append('date', this.form.date);
            data.append('moyen_paiement', this.form.moyen_paiement);
            data.append('note', this.form.note);
            data.append('file[]', this.attachmentsDecharge);

            for (let i = 0; i < this.attachmentsDecharge.length; i++) {
                data.append('files' + i, this.attachmentsDecharge[i]);
            }
            data.append('TotalFiles', this.attachmentsDecharge.length);

            let action = "create";

            if(this.editmode){
                data.append('additionalFile', JSON.stringify(this.editFile));
                action = "edit/"+this.form.id;
            }

            this.isLoading = true;

     
            axios.post("/operations/versements/"+action, data,  {
                headers: {
                    'Content-Type': 'multipart/form-data'
                } 
            }).then(response => {
              
                if(response.data.code==0){
                    this.isLoading = false;
                    this.$refs.closePopup.click();
                    this.reset();

                    Vue.swal.fire(
                      'succés!',
                      'Versement ajouté avec succés!',
                      'success'
                    );

                    this.getVersements();

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
        onInputSelectProprio(value) {
          if (!value) {
            this.proprioSelected = null;
            this.proprioID = '';
            this.getVersements(); // 💡 appel de ton action de réinitialisation
          }
        },
        onProprioChoisi(proprio){
          this.proprioSelected = proprio;
          this.proprioID = proprio.proprio_id;
          this.getVersements();
        },
         onSelectBien(proprio){
         this.biensFiltres = this.biens
          .filter(x => x.bien_proprio === proprio.proprio_id) // filtre selon le propriétaire
          .map(function (x) {
            x.item_bien = x.bien_nom + ' ' + x.bien_adresse + ' ' + x.bien_ville + ' (' + x.bien_id + ')';
            return x;
          });
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
         deleteVersement(v){
            Vue.swal.fire({
              title:"Suppression Versement ",
              text: "Attention!!! cette opération est irréversible.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#3085d6',
              confirmButtonText: 'Oui, supprimer!'
            }).then((result) => {
            if (result.isConfirmed) {

                axios.delete('/operations/versements/deleteversement/'+v.identifiant).then(response => {
                    console.log(response);
                    if(response.data.code==0){
                         Vue.swal.fire(
                          'Suppression',
                          'Versement supprimé avec succés',
                          'success'
                        );
                        this.getVersements();
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
    mounted() {
        console.log(this.listProprio) ;
        this.listProprio.map(function (x){
          return x.item_proprio = (x.proprio_nom + ' ' + x.proprio_prenom + ' (' +x.proprio_id +')');
        });  

        console.log("biens", this.biens)

        this.biens.map(function (x){
          return x.item_bien = x.biens_id;
        }); 
        this.getVersements();
    }
}
</script>
