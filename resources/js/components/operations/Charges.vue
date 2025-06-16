<template>
    <div>
        <div class="d-flex justify-content-between align-items-center mb-3">
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
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addNew" v-on:click="newModal" ><i class="fa fa-plus"></i> Nouvelle Charge / Frais</button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
              <thead class="bg-white">
                <tr>
                    <th>Propriétaire</th>
                    <th>Bien</th>
                    <th>Local</th>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Date</th>
                    <th>Ajouté par</th>
                    <th class="text-right">Action</th>
                </tr>
                <tr>
                    <th colspan="9" class="position-relative p-0">
                        <div class="loader-line" :class="[isLoading?'d-block':'d-none']"></div>
                    </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="charge in charges.data" :key="charge.identifiant" >
                     <td class="align-middle">
                        {{charge.proprio_nom}} {{charge.proprio_prenom}}
                    </td>
                    <td class="align-middle">
                        {{ charge.bien_nom }}, {{ charge.bien_adresse }},n° {{ charge.bien_numero }}
                    </td>
                    <td class="align-middle text-uppercase">
                        {{ charge.local_type }}    
                    </td>
                    <td class="align-middle">{{ charge.type }}</td>
                    <td class="align-middle">{{ helper_separator_amount(charge.montant) }}</td>
                    <td class="align-middle">{{ charge.date }}</td>
                    <td class="align-middle">
                        {{ charge.user }}
                    </td>
                    <td class="text-right align-middle">

                        <button  title="Fichier" v-if="charge.fichier.length > 0" class="btn btn-info mr-2 cursor-pointer" data-toggle="modal" data-target="#modalFichier" v-on:click="showDocument('assets/factures/'+charge.fichier[0])">
                                <i class="fa fa-file-pdf"></i>
                        </button>
                        <button  v-if="charge.note != '0' && charge.note != '' && charge.note != 'undefined'" class="btn btn-primary mr-2 cursor-pointer" v-on:click="showInfo(charge.note)">
                            <i class="fa fa-info"></i>
                        </button>
                        <button class="btn btn-danger" v-on:click="deleteDecharge(charge)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
              </tbody>
            </table>
        </div>
        <div class="d-flex mt-4 justify-content-center">
            <pagination
                :data="charges"
                :limit=10
                @pagination-change-page="getCharges"
            ></pagination>
        </div>

         <!-- Modal -->
        <div class="modal fade" id="addNew" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="addNew" aria-hidden="true">
            
            <div class="modal-dialog modal-xl position-relative" role="document">
                <div class="loader-line" :class="[isLoading?'d-block':'d-none']"></div>
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-uppercase" v-show="!editmode"><strong><span class="text-primary"><u>Ajout</u></span> Charge & Frais</strong></h5>
                    <h5 class="modal-title" v-show="editmode"><strong><span class="text-primary"><u>Editer</u></span> Locataire</strong></h5>
                    <button type="button" class="close" ref="closePopup" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 

                <form @submit.prevent="createCharges()">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="d-block text-uppercase text-info border-bottom titleform border-info">Propriétaire Lié</label>
                                <div class="form-group">
                                    <label>Choisir un propriétaire <span class="text-danger">*</span></label>
                                     <v-select :class="{ 'border-danger': isSubmitted && !$v.form.proprio.required }" v-model="form.proprio" :options="listProprio" :reduce="(option) => option" label="item_proprio"></v-select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                 <label class="pt-2">Choisir un bien <span class="text-danger">*</span></label>
                                     <v-select  :class="{ 'border-danger': isSubmitted && !$v.form.bien.required }" v-model="form.bien" :options="biens" :reduce="(option) => option" label="item_bien"></v-select>
                            </div>
                            <div class="col-md-6">
                                <label class="pt-2">Choisir un local</label>
                                     <v-select v-model="form.local" :options="locals" :reduce="(option) => option" label="item_local"></v-select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="d-block text-uppercase text-info border-bottom titleform border-info">Charges / Frais</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="pt-2">Type</label>
                                <div class="d-flex align-items-center">
                                  <select class="form-control" v-model="form.type">
                                      <option value="">Choisir</option>
                                      <option v-for="charge, index in typeCharge" :value="index">{{ charge }}</option>
                                  </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="pt-2">Montant</label>
                                <div class="d-flex align-items-center">
                                    <input v-model="form.montant" type="text"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-6">
                                <label class="pt-2">Date </label>
                                <div class="d-flex align-items-center">
                                    <date-picker v-model="form.date" class="w-100"  required valueType="YYYY-MM-DD" input-class="form-control w-100" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <label class="pt-2">Note <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center">
                                    <textarea v-model="form.note" class="form-control"  :class="{ 'border-danger': isSubmitted && !$v.form.note.required }"></textarea>
                                </div>
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pt-2 mb-0">Facture / Décharge</label>
                                    <input name="file" multiple type="file" ref="attachmentsDecharge"
                                        class="form-control border-0 pl-0" v-on:change="handleFileUpload()">
                                </div>
                            </div>
                        </div> 
                       
                    </div>
                    
                    <div class="modal-footer justify-content-center">
                        <button v-show="editmode" type="submit" class="btn btn-success" :disabled="isLoad ? true: false">Enregister</button>
                        <button v-show="editmode" type="button" class="btn btn-warning"  data-dismiss="modal" @click="reset()">Annuler</button>
                        <button v-show="!editmode" type="submit" class="btn btn-success">Créer</button>
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
    name: "Charges",
    props: ["listProprio", "typeCharge"],
    components: { DatePicker, VueCountryDropdown, modalDocument, modalInfo },
    data () {
        return {
            editmode: false,
            isLoading: false,
            isLoad: false,
            isSubmitted: false,
            charges : {},
            paiements: [],
            paginate: 5,
            editmode: false,
            form: {
                id : '',
                proprio : '',
                bien: '',
                local: '',
                montant: '',
                decharge: null,
                date: null,
                type: '',
                type_autre: '',
                note: ''
               
            },
            biens: [],
            locals: [],
            attachmentsDecharge: [],
            editFile: [],
            proprioSelected: null,
            proprioID: '',
        }
    },
    validations: {
        form : {
            proprio: { required },
            montant: { required },
            bien: { required },
            note: { required }

        },
        
    },
    watch: {
       paginate: function(){
            this.getCharges();
       },
       'form.proprio': function(value){
            if(value) this.getBienByProprio(value.proprio_id);
       },
       'form.bien': function(value){
            if(value) this.getLocal(value.bien_id);
       },
       'form.local': function(item){
            this.form.montant_ht=0;
            for(var i=0; i<item.length; i++){

                var obj = item[i];
                
                this.form.montant_ht += parseInt(obj.local_prix_loyer);
            }
       },
      
    },
    methods: {
        getCharges(page=1){
           this.isLoading = true;
           const params = {};
            if (this.proprioID) params.proprioID = this.proprioID;
            axios.get("/operations/charges?paginate="+this.paginate+'&page=' + page, {params}).then(responses => {
               console.log(responses);
               this.charges = responses.data;
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
            this.form.note = '';
            this.form.type_autre = '';
            this.form.local = '';
        },
        editCharge(oper){

        },
        handleFileUpload(){
            this.attachmentsDecharge = [];
            for(var i=0; i<this.$refs.attachmentsDecharge.files.length;i++){
                this.attachmentsDecharge.push(this.$refs.attachmentsDecharge.files[i])
            }
        },
        createCharges(){
            this.isSubmitted = true;

            // stop here if form is invalid
            this.$v.form.$touch();

            if (this.$v.form.$invalid) {
                return;
            }          
            this.isLoad = true;

            const data = new FormData();
            data.append('proprio', this.form.proprio.proprio_id);
            data.append('bien', this.form.bien.bien_id);
            data.append('local', this.form.local.local_id);
            data.append('montant', this.form.montant);
            data.append('date', this.form.date);
            data.append('type', this.form.type);
            data.append('type_autre', this.form.type_autre);
            data.append('note', this.form.note);
            data.append('file[]', this.attachmentsDecharge);

            data.append('proprio_email', this.form.proprio.proprio_email);
            data.append('proprio_nom', this.form.proprio.proprio_nom);
            data.append('proprio_prenom', this.form.proprio.proprio_prenom);

            for (let i = 0; i < this.attachmentsDecharge.length; i++) {
                data.append('files' + i, this.attachmentsDecharge[i]);
            }
            data.append('TotalFiles', this.attachmentsDecharge.length);

            let action = "create";

            if(this.editmode){
                data.append('additionalFile', JSON.stringify(this.editFile));
                action = "modify/"+this.form.id;
            }

            this.isLoading = true;

     
            axios.post("/operations/charge/"+action, data,  {
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
                      'Opération ajoutée avec succés!',
                      'success'
                    );

                    this.getCharges();

                }else{
                     Vue.swal.fire(
                      'error!',
                      response.data.message,
                      'error'
                    )
                }
                this.isSubmitted = false;
                this.isLoad = false;

            });
        },
        getBienByProprio(id){
             axios.get("/operations/getbien/"+id.toString()).then(responses => {
                if(responses.data.code=='0'){
                    this.biens = responses.data.data;
                    this.biens.map(function (x){
                      return x.item_bien = x.bien_nom+' '+x.bien_adresse + ' ' + x.bien_ville + ' (' +x.bien_id +')';;
                    });   
                }
                
              
            }).catch(errors => { 
            

            })
        },
        getLocal(id){
             axios.get("/bail/getLocal/"+id.toString()+"/"+this.form.proprio.proprio_id+"?showAll=1").then(responses => {
                if(responses.data.code=='0'){
                    this.form.agence = responses.data.agence;
                    this.form.pers = responses.data.responsable;
                    this.locals = responses.data.locaux;
                    this.locals.map(function (x){
                      return x.item_local = x.local_type_local.toUpperCase()+' ('+x.local_type_location.toUpperCase()+')';
                    });   
                }else{
                    alert(responses.data.message);
                }
                
              
            }).catch(errors => { 
            

            })
        },
         deleteDecharge(d){
            Vue.swal.fire({
              title:"Suppression Charge ",
              text: "Attention!!! cette opération est irréversible.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#3085d6',
              confirmButtonText: 'Oui, supprimer!'
            }).then((result) => {
            if (result.isConfirmed) {

                axios.delete('/operations/charges/deletedecharge/'+d.identifiant).then(response => {
                    console.log(response);
                    if(response.data.code==0){
                         Vue.swal.fire(
                          'Suppression',
                          'Charge supprimé avec succés',
                          'success'
                        );
                        this.getCharges();
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
        },
        showDocument(file){
             EventBus.$emit('VIEW_DOCUMENT', {
                path: file,
                title: 'Facture'
            });
        },
        showInfo(note){
            EventBus.$emit('VIEW_TEXT', {
                texte: note,
                title: 'Info'
            });
        },
         onInputSelectProprio(value) {
          if (!value) {
            this.proprioSelected = null;
            this.proprioID = '';
            this.getCharges(); // 💡 appel de ton action de réinitialisation
          }
        },
        onProprioChoisi(proprio){
          this.proprioSelected = proprio;
          this.proprioID = proprio.proprio_id;
          this.getCharges();
        }

    },
    mounted() {
        console.log(this.listProprio) ;
        this.listProprio.map(function (x){
          return x.item_proprio = x.proprio_nom + ' ' + x.proprio_prenom + ' (' +x.proprio_id +')';
        });  

        this.biens.map(function (x){
          return x.item_bien = x.biens_id;
        }); 
        this.getCharges(); 
    }
}
</script>
