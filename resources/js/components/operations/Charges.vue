<template>
    <div>
        <div class="d-flex justify-content-between mb-3">
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
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addNew" v-on:click="newModal" ><i class="fa fa-plus-square"></i> Nouvelle Charge / Frais</button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
              <thead class="bg-white">
                <tr>
                    <th>Propriétaire</th>
                    <th>Bien</th>
                    <th>Local</th>
                    <th>Type</th>
                    <th>Montant Total</th>
                    <th>Note</th>
                    <th>Date</th>
                    <th>User</th>
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
                    <td class="align-middle">
                        {{ charge.local_type }}    
                    </td>
                    <td class="align-middle">{{ charge.type }}</td>
                    <td class="align-middle">{{ helper_separator_amount(charge.montant) }}</td>
                    <td class="align-middle">{{ charge.note }}</td>
                    <td class="align-middle">{{ charge.date_creation }}</td>
                    <td class="align-middle">
                        {{ charge.user }}
                    </td>
                    <td class="text-right align-middle">
                        <button class="btn btn-danger"><i class="fa fa-trash"></i></button>
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

                                    <label class="pt-2">Choisir un bien <span class="text-danger">*</span></label>
                                     <v-select  :class="{ 'border-danger': isSubmitted && !$v.form.bien.required }" v-model="form.bien" :options="biens" :reduce="(option) => option" label="item_bien"></v-select>  

                                     <label class="pt-2">Choisir un local <span class="text-danger">*</span></label>
                                     <v-select v-model="form.local" :options="locals" :reduce="(option) => option" label="item_local"></v-select>  
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="d-block text-uppercase text-info border-bottom titleform border-info">Charges / Frais</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="pt-2">Type</label>
                                <div class="d-flex align-items-center">
                                  <select class="form-control" v-model="form.type">
                                      <option value="">Choisir</option>
                                      <option v-for="charge, index in typeCharge" :value="index">{{ charge }}</option>
                                  </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="pt-2">Montant</label>
                                <div class="d-flex align-items-center">
                                    <input v-model="form.montant" type="text"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="pt-2">Note</label>
                                <div class="d-flex align-items-center">
                                    <textarea v-model="form.note" class="form-control"></textarea>
                                </div>
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="pt-2 mb-0">Décharge</label>
                                    <input name="file" multiple type="file" ref="form.decharge" 
                                        class="form-control border-0 pl-0" v-on:change="handleFileUpload()">
                                </div>
                            </div>
                        </div> 
                       
                    </div>
                    
                    <div class="modal-footer justify-content-center">
                        <button v-show="editmode" type="submit" class="btn btn-success">Enregister</button>
                        <button v-show="editmode" type="button" class="btn btn-warning"  data-dismiss="modal" @click="reset()">Annuler</button>
                        <button v-show="!editmode" type="submit" class="btn btn-success">Créer</button>
                        <button  v-show="!editmode" type="button" class="btn btn-info btn" @click="reset()">Réinitialiser</button>
                        <button  v-show="!editmode" type="button" class="btn btn-secondary " @click="reset()" data-dismiss="modal">Annuler</button>
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
    name: "Charges",
    props: ["listProprio", "typeCharge"],
    components: { DatePicker, VueCountryDropdown },
    data () {
        return {
            editmode: false,
            isLoading: false,
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
                type: '',
                type_autre: ''
               
            },
            biens: [],
            locals: [],
            attachmentsDecharge: [],
            editFile: []
        }
    },
    validations: {
        form : {
            proprio: { required },
            montant: { required },
            bien: { required }

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
            axios.get("/operations/charges?paginate="+this.paginate+'&page=' + page).then(responses => {
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

            const data = new FormData();
            data.append('proprio', this.form.proprio.proprio_id);
            data.append('bien', this.form.bien.bien_id);
            data.append('local', this.form.local.local_id);
            data.append('montant', this.form.montant);
            data.append('type', this.form.type);
            data.append('type_autre', this.form.type_autre);
            data.append('note', this.form.note);
            data.append('file[]', this.attachmentsDecharge);

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
             axios.get("/bail/getLocal/"+id.toString()+"/"+this.form.proprio.proprio_id).then(responses => {
                if(responses.data.code=='0'){
                    this.form.agence = responses.data.agence;
                    this.form.pers = responses.data.responsable;
                    this.locals = responses.data.locaaux;
                    this.locals.map(function (x){
                      return x.item_local = x.local_type_local.toUpperCase()+' ('+x.local_type_location.toUpperCase()+')';
                    });   
                }else{
                    alert(responses.data.message);
                }
                
              
            }).catch(errors => { 
            

            })
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
