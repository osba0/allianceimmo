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
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addNew" v-on:click="newModal" ><i class="fa fa-plus"></i> Nouveau Contrat de Bail</button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
              <thead class="bg-white">
                <tr>
                    <th>Identifiant</th>
                    <th>Bailleur</th>
                    <th>Locataire</th>
                    <th class="text-nowrap">Type Bail</th>
                    <th>Durée</th>
                    <th>Date Expiration</th>
                    <th class="text-right">Action</th>
                </tr>
                <tr>
                    <th colspan="9" class="position-relative p-0">
                        <div class="loader-line" :class="[isLoadingTab?'d-block':'d-none']"></div>
                    </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="bail in bails.data" :key="bail.identifiant">
                    <td class="align-middle">
                        <h5 class="m-0">
                            <label class="badge badge-primary m-0">{{bail.identifiant}}</label>
                        </h5>
                    </td>
                    <td class="align-middle">
                        {{bail.proprio_nom}} {{bail.proprio_prenom}}
                    </td>
                    <td class="align-middle"> {{bail.locataire_nom}} {{bail.locataire_prenom}}</td>
                    <td class="align-middle text-uppercase">{{bail.bail_type}}</td>
                    <td class="align-middle">
                        <div class="d-flex align-items-center">
                            <div class="progress" :data-percentage="((bail.bail_expiration/bail.bail_difference)*100).toFixed(0)">
                                <span class="progress-left">
                                    <span class="progress-bar"></span>
                                </span>
                                <span class="progress-right">
                                    <span class="progress-bar"></span>
                                </span>
                                <div class="progress-value">
                                    <div>
                                        {{ bail.bail_expiration }} j
                                    </div>
                                </div>
                            </div>
                            <!--div class="mr-2">
                                <ul class="m-0">
                                    <li title="Durée"><i class="fa fa-hourglass" aria-hidden="true"></i>  {{bail.bail_duree}} An(s)</li>
                                    <li title="Date Début"><i class="fa fa-hourglass-start text-success" aria-hidden="true"></i> {{bail.bail_date_debut}}</li>
                                    <li title="Date Fin"><i class="fa fa-hourglass-end text-danger" aria-hidden="true"></i> {{bail.bail_date_fin}}</li>
                                </ul>
                            </div-->

                        </div>
                    </td>
                    <td class="align-middle">{{bail.bail_date_fin}}</td>
                    <td class="text-right align-middle">
                        <button title="Le contrat de Bail" data-toggle="modal" data-target="#modalFichier" v-on:click="showDocument(bail.bail_fichier)" v-if="bail.bail_fichier !=''" class="btn btn-info">
                           
                            <i class="fa fa-file-pdf" ></i>
                              
                        </button>
                        <button class="btn btn-danger" @click="deleteProprio(bail)"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
              </tbody>
            </table>
        </div>
        <div class="d-flex mt-4 justify-content-center">
            <pagination
                :data="bails"
                :limit=10
                @pagination-change-page="getBail"
            ></pagination>
        </div>

        <!-- Modal File Bail -->
        <modalDocument></modalDocument>
        <!-- Modal -->
        <div class="modal fade" id="addNew" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="addNew" aria-hidden="true">
            
            <div class="modal-dialog modal-xl position-relative" role="document">
                <div class="loader-line" :class="[isLoading?'d-block':'d-none']"></div>
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-uppercase" v-show="!editmode"><strong><span class="text-primary"><u>Nouveau</u></span> Contrat de Bail</strong></h5>
                    <h5 class="modal-title" v-show="editmode"><strong><span class="text-primary"><u>Editer</u></span> Bail</strong></h5>
                    <button type="button" class="close" ref="closePopup" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 

                <form>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="orientation mr-3 text-left border-right pr-2">
                                        <label class="text-uppercase text-info titleform border-info pt-2">INFO Propriétaire</label>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="form-group">
                                            <label>Choisir un propriétaire <span class="text-danger">*</span></label>
                                             <v-select :class="{ 'border-danger': isSubmitted && !$v.form.proprio.required }" v-model="form.proprio" :options="listProprio" :reduce="(option) => option" label="item_proprio"></v-select> 

                                            <label class="pt-2">Choisir un bien <span class="text-danger">*</span></label>
                                             <v-select  :class="{ 'border-danger': isSubmitted && !$v.form.bien.required }" v-model="form.bien" :options="biens" :reduce="(option) => option" label="item_bien"></v-select>  

                                             <label class="pt-2">Choisir un local <span class="text-danger">*</span></label>
                                             <v-select multiple :class="{ 'border-danger': isSubmitted && !$v.form.local.required }" v-model="form.local" :options="locals" :reduce="(option) => option" label="item_local"></v-select>  
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">                              
                                <div class="d-flex">
                                    <div class="orientation mr-3 text-left border-right pr-2">
                                        <label class="text-uppercase text-info titleform border-info pt-2">Infos Locataire</label>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="form-group">

                                            <label>Choisir un locataire <span class="text-danger">*</span></label> 
                                            <v-select  v-model="form.locataire" :class="{ 'border-danger': isSubmitted && !$v.form.locataire.required }" :options="listLocataire" :reduce="(option) => option" label="item_locataire"></v-select> 
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="orientation mr-3 text-left border-right pr-2">
                                <label class="text-uppercase text-info titleform border-info pt-2">Détails Bail</label>
                            </div>
                            <div class="flex-grow-1">
                                <div class="row">
                                    <div class="col-md-6 d-flex justify-content-between">
                                        <div class="form-group w-49">
                                            <label>Type de Bail</label>
                                            <div class="d-flex align-items-center">
                                                <select class="form-control" v-model="form.type_bail">
                                                    <option>Choisir</option>
                                                    <option value="habitation">Habitation</option>
                                                    <option value="commercial">Commercial</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group w-49">
                                            <label>Durée</label>
                                            <div class="d-flex align-items-center">
                                                <input v-model="form.duree" type="number"
                                                class="form-control">
                                                <span class="px-2">An(s)</span>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="col-md-6 justify-content-between d-flex">
                                        <div class="form-group w-49">
                                            <label>Date Début</label>
                                            <date-picker v-model="form.date_debut" class="w-100"  required valueType="YYYY-MM-DD" input-class="form-control w-100" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>
                                        </div>
                                        <div class="form-group w-49">
                                            <label>Date Fin</label>
                                            <date-picker v-model="form.date_fin" class="w-100"  required valueType="YYYY-MM-DD" input-class="form-control w-100" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label title="Montant Hors taxe">Montant HT </label>
                                            <div class="d-flex align-items-center">
                                                <input v-model="form.montant_ht" type="text"
                                                class="form-control">
                                                <span class="px-2">FCFA</span>
                                            </div>
                                        </div>
                                      
                                    </div>
                                     <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Montant Caution</label>
                                            <div class="d-flex align-items-center">
                                                <input v-model="form.caution_mnt_ht" type="text"
                                                class="form-control">
                                                <span class="px-2">FCFA</span>
                                            </div>
                                        </div>
                                      
                                    </div>
                                </div> 
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Frais Retard</label>
                                             <div class="d-flex align-items-center">
                                                <input v-model="form.frais_retard" type="text"
                                                    class="form-control">
                                                    <span class="px-2">FCFA</span>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-between">
                                        <div class="form-group w-49">
                                            <label>Garant </label>
                                            <div class="d-flex align-items-center">
                                                <input v-model="form.depot_garantie" type="text"
                                                    class="form-control">
                                            </div>
                                        </div>
                                       <div class="form-group w-49">
                                        <label>Piéce Garant</label>
                                             <div class="d-flex align-items-center">
                                                <input v-model="form.garant" type="text"
                                                    class="form-control">
                                                   
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                        
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button v-show="editmode" type="submit" class="btn btn-success">Enregister</button>
                        <button v-show="editmode" type="button" class="btn btn-warning"  data-dismiss="modal" @click="reset()">Annuler</button>
                        <button v-show="!editmode" @click="previsualiser()" data-toggle="modal" data-target="#previsualitionBail"  type="button" class="btn btn-warning" :disabled="!iscompleted">Prévisualiser</button>
                        <button  v-show="!editmode" type="button" class="btn btn-info btn" @click="reset()">Réinitialiser</button>
                        <button  v-show="!editmode" type="button" class="btn btn-secondary " @click="reset()" data-dismiss="modal">Annuler</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>


        <!-- modal carousel -->
        <modalCarousel></modalCarousel>

        <!-- Modal Prévisualisation-->
        <ModalConfirmationBail></ModalConfirmationBail>
        
    </div>
</template>

<script>
import DatePicker from 'vue2-datepicker';
import VueCountryDropdown from 'vue-country-dropdown';
import 'vue2-datepicker/index.css';
import modalCarousel from '../../components/modal/carousel.vue';
import modalDocument from '../../components/modal/document.vue';
import { EventBus } from "../../event-bus"; 
import { required, email, minLength, between } from 'vuelidate/lib/validators';

import ModalConfirmationBail from '../modeles/bail/Default.vue';

// Pdf
import { PdfMakeWrapper, Table, QR, Img} from 'pdfmake-wrapper';

import { ITable } from 'pdfmake-wrapper/lib/interfaces'; 

import pdfFonts from "pdfmake/build/vfs_fonts";

export default {
    name: "Bail",
    props: ["listProprio", "listLocataire"],
    components: { DatePicker, VueCountryDropdown, modalCarousel, ModalConfirmationBail, modalDocument },
    data () {
        return {
            editmode: false,
            bails : {},
            form: {
                id : '',
                proprio : '',
                position: '',
                type_bail: '',
                bien: '',
                local: '',
                agence:'',
                locataire: '',
                pers:'',
                duree: '',
                date_debut: '',
                date_fin:'',
                montant_ht: 0,
                caution_mnt_ht: 0,
                depot_garantie: '',
                garant: '',
                frais_retard: '',
                fichier: null
            },
            attachmentsFichier: [],
            info: {},
            isSubmitted: false,
            isLoading: false,
            paginate: 5,
            editFichier: [],
            isLoadingTab: false,
            bail_previsualise: null,
            iscompleted: false,
            biens: [],
            locals: []
        }
    },
    validations: {
        form : {
            agence:      { required },
            proprio:     { required },
            bien:        { required },
            duree:       { required },
            local:       { required },
            locataire:   { required }

        }
    },
    watch: {
       paginate: function(){
            this.getBail();
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
                console.log(">>>>----->>>", obj.local_prix_loyer);
                this.form.montant_ht += parseInt(obj.local_prix_loyer);
            }
            console.log(">>>>", this.form.montant_ht);
       },
       'form.montant_ht': function(value){
            this.form.montant_ht = this.helper_separator_amount(value)
       },
       form:  {
            handler: function (el) {
                console.log("BIEN>>", el.bien, ">>PROP",el.proprio, '>>>>AG',el.agence,'>>>PERS', this.form.pers);
                if(this.form.proprio  && this.form.bien && this.form.local && this.form.locataire && this.form.duree  && this.form.date_debut  && this.form.date_fin   && this.form.montant_ht  && this.form.frais_retard && this.form.caution_mnt_ht){
                    this.iscompleted = true;
                }else{
                    this.iscompleted = false;
                }
                // whenever the car price changes, this function will be executed
            },
            deep: true
        }
    },
    methods: {
       
        createBail(){

            this.isSubmitted = true;
            // stop here if form is invalid
            this.$v.form.$touch();

            if (this.$v.form.$invalid) {
                return;
            }

            console.log("dd", this.form.local);

            // get ID local
            var listLocal=[];

            for(var i=0; i<this.form.local.length;i++){
                listLocal.push(this.form.local[i].local_id);
            }

            const data = new FormData();

            data.append('proprio', this.form.proprio.proprio_id);
            data.append('bien', this.form.bien.bien_id);
            data.append('agence', this.form.agence.agence_id);
            data.append('type_bail', this.form.type_bail);
            data.append('local', JSON.stringify(listLocal));
            data.append('locataire', this.form.locataire.locat_id);
            data.append('duree', this.form.duree);
            data.append('date_debut', this.form.date_debut);
            data.append('date_fin', this.form.date_fin);
            data.append('montant_ht', this.form.montant_ht);
            data.append('caution_mnt_ht', this.form.caution_mnt_ht);   
            data.append('depot_garantie', this.form.depot_garantie); 
            data.append('garant', this.form.garant);    
            data.append('frais_retard', this.form.frais_retard);      

            let action = "create";

            this.isLoading = true;

     
            axios.post("/bail/"+action, data,  {
                headers: {
                    'Content-Type': 'multipart/form-data'
                } 
            }).then(response => {
                if(response.data.code==0){
                    // Generer le contrat avec le bon ID et l'enregistrer
                    this.generateBailWithIDCreated(response.data.id_bail);

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
        onSelect({name, iso2, dialCode}) {
           console.log(name, iso2, dialCode);
           this.form.pays = iso2; 
        },
        onSelect({name, iso2, dialCode}) {
           console.log(name, iso2, dialCode);
           this.form.pays = iso2; 
        },
        newModal(){
          this.editmode = false;
        },
        getBail(page=1){
            this.isLoadingTab = true;
            axios.get("/bail/listing?paginate="+ this.paginate+'&page=' + page).then(responses => {
                console.log(responses);
                this.bails = responses.data;
                this.isLoadingTab = false;
            }).catch(errors => { 
                this.isLoadingTab = true;
            // react on errors.

            })
        },
        reset(){

            this.form.id = '';
            this.form.proprio = '';
            this.form.position = '';
            this.form.bien = '';
            this.form.local = '';
            this.form.agence = '';
            this.form.locataire = '';
            this.form.pers = '';
            this.form.duree = '';
            this.form.date_debut = '';
            this.form.date_fin = '';
            this.form.montant_ht = 0;
            this.form.caution_mnt_ht = 0;
            this.form.depot_garantie = '';
            this.form.garant = '';
            this.form.frais_retard = '';
            this.form.fichier = null;
          
        },
        handleFileUpload(){
            this.attachmentsPhotos = [];
            for(var i=0; i<this.$refs.attachmentsPhotos.files.length;i++){
                this.attachmentsPhotos.push(this.$refs.attachmentsPhotos.files[i])
            }
        },
        handleFileUploadPerso(){
            this.attachmentsPhotosPerso = [];
            for(var i=0; i<this.$refs.attachmentsPhotosPerso.files.length;i++){
                this.attachmentsPhotosPerso.push(this.$refs.attachmentsPhotosPerso.files[i])
            }
        },
        edit(current){
            this.editmode = true;

        },
        setKyc(prop){
             // get photo
            EventBus.$emit('VIEW_CAROUSEL', { 
                kyc: prop.kyc
            });
        },
        view(item){
            EventBus.$emit('VIEW_INFO_LOCATAIRE', { 
                info:  item
            });
        },
        supprimerPhoto(name, photo, id, type){
            Vue.swal.fire({
              title:"Suppression Photo ",
              text: "Attention!!! cette opération est irréversible.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#3085d6',
              confirmButtonText: 'Oui, supprimer!'
            }).then((result) => {
            if (result.isConfirmed) {
                const data = new FormData();
                data.append('identifiant', id);
                data.append('type', type);
                data.append('photos', JSON.stringify(photo)); 
                data.append('namePhoto', name);
                axios.post('/locat/remphoto', data).then(response => {
                    console.log(response);
                    if(response.data.code==0){
                        this.getBail(); 
                         Vue.swal.fire(
                          'Suppression',
                          'Photo supprimé avec succés',
                          'success'
                        );
                        if(type=='perso')
                            this.editPhotoPerso = response.data.file;
                        else
                            this.editPhotoPiece = response.data.file;



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
        deleteProprio(proprio){
            Vue.swal.fire({
              title:"Suppression Bail ",
              text: "Attention!!! cette opération est irréversible.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#3085d6',
              confirmButtonText: 'Oui, supprimer!'
            }).then((result) => {
            if (result.isConfirmed) {
    
                axios.delete('/proprio/delete/'+proprio.identifiant).then(response => {
                    console.log(response);
                    if(response.data.code==0){
                         Vue.swal.fire(
                          'Suppression',
                          'Bail supprimé avec succés',
                          'success'
                        );
                        this.getBail();  
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
        onSelectNaissance({name, iso2, dialCode}) {
           console.log(name, iso2, dialCode);
           this.form.pays_naissance = iso2; 
        },
        onSelectResidance({name, iso2, dialCode}) {
           console.log(name, iso2, dialCode);
           this.form.pays = iso2; 
        },
        getBienByProprio(id){
            this.form.bien = '';
            axios.get("/bail/getbien/"+id.toString()).then(responses => {
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
            this.form.local = '';
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
        ,
        previsualiser(){
            EventBus.$emit('VIEW_PREV_BAIL', { 
                bail: this.form
            });
        },
        previsualiser(){
            EventBus.$emit('VIEW_PREV_BAIL', { 
                bail: this.form
            });
        },
        generateBailWithIDCreated(id_bail){
            this.form.id = id_bail;
            EventBus.$emit('GENERATED_BAIL', { 
                bail: this.form
            });
        },
        fileBailGenerate(file_generate, namefile){
            const data = new FormData();

            data.append('id_bail', this.form.id);
            data.append('file_genered', file_generate);
            data.append('name_file', namefile);
            
            let action = "create_file_bail";

            axios.post("/bail/"+action, data,  {
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
                      'Contrat de bail crée avec succés!',
                      'success'
                    );
                    this.getBail(); 

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
        showDocument(file){
             EventBus.$emit('VIEW_DOCUMENT', { 
                path: file,
                title: 'Contrat de bail'
            });
        }

    },
    mounted() {
        this.getBail(); 

        console.log(this.listProprio) ;
        this.listProprio.map(function (x){
          return x.item_proprio = x.proprio_nom + ' ' + x.proprio_prenom + ' (' +x.proprio_id +')';
        });  

        this.biens.map(function (x){
          return x.item_bien = x.biens_id;
        }); 

        this.listLocataire.map(function (x){
          return x.item_locataire = x.locat_nom + ' ' + x.locat_prenom
        });  

        EventBus.$on('SAVE_BAIL', (event) => {

            this.createBail(event.file_genered, event.name_file);
           
        });  
        
        EventBus.$on('SAVE_FILE_BAIL', (event) => {

            this.fileBailGenerate(event.file_genered_save, event.name_file_save);
           
        });  
        
    }
}
</script>
