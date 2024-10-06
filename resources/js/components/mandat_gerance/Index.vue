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
            <button v-if="hasPermission('Mandat.Ajouter')" type="button" class="btn btn-primary" data-toggle="modal" data-target="#addNew" v-on:click="newModal" ><i class="fa fa-plus"></i> Nouveau Mandat de Gérance</button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
              <thead class="bg-white">
                <tr>
                    <th>Identifiant</th>
                    <th>Propriétaire</th>
                    <th>Mandataire</th>
                    <th>Bien</th>
                    <th>Durée</th>
                    <th>Expiration</th>
                    <th>User</th>
                    <th class="text-right">Action</th>
                </tr>
                <tr>
                    <th colspan="8" class="position-relative p-0">
                        <div class="loader-line" :class="[isLoadingTab?'d-block':'d-none']"></div>
                    </th>
                </tr>
              </thead>
              <tbody>
                <template v-if="!mandats.data || !mandats.data.length">
                    <tr><td colspan="8" class="bg-white text-center" v-if="checking">Aucun résultat!</td></tr>
                </template>
                <tr v-for="mandat in mandats.data" :key="mandat.identifiant">
                    <td class="align-middle">
                        <h5 class="m-0">
                            <label class="badge badge-primary m-0">{{mandat.identifiant}}</label>
                        </h5>
                    </td>
                    <td class="align-middle">
                        {{mandat.proprio_nom}} {{mandat.proprio_prenom}}
                    </td>
                    <td class="align-middle">{{mandat.agence}}</td>
                    <td class="align-middle">
                        <strong>{{mandat.bien_nom}}</strong> {{mandat.bien_adresse}}, n°{{mandat.bien_numero}}
                    </td>
                    <td class="align-middle">
                        <div class="d-flex align-items-center">
                            <div class="progress" :data-percentage="((mandat.mandat_expiration/mandat.mandat_difference)*100).toFixed(0)">
                                <span class="progress-left">
                                    <span class="progress-bar"></span>
                                </span>
                                <span class="progress-right">
                                    <span class="progress-bar"></span>
                                </span>
                                <div class="progress-value">
                                    <div>
                                        {{ mandat.mandat_expiration }} j
                                    </div>
                                </div>
                            </div>
                            <!--div class="mr-2">
                                <ul class="m-0">
                                    <li title="Durée"><i class="fa fa-hourglass" aria-hidden="true"></i>  {{mandat.mandat_duree}} An(s)</li>
                                    <li title="Date Début"><i class="fa fa-hourglass-start text-success" aria-hidden="true"></i> {{mandat.mandat_date_debut}}</li>
                                    <li title="Date Fin"><i class="fa fa-hourglass-end text-danger" aria-hidden="true"></i> {{mandat.mandat_date_fin}}</li>
                                </ul>
                            </div-->

                        </div>
                    </td>
                    <td class="align-middle">
                        {{mandat.mandat_date_fin}}
                    </td>
                    <td class="align-middle text-uppercase">
                        {{mandat.user}}
                    </td>
                    <td class="text-right align-middle">
                        <button title="Le mandat de Gérance" data-toggle="modal" data-target="#modalFichier" v-on:click="showDocument(mandat.mandat_fichier)" v-if="mandat.mandat_fichier !=''" class="btn btn-info">
                           
                            <i class="fa fa-file-pdf" ></i>
                              
                        </button>
                        <span v-else><i class="fa fa-camera-retro" aria-hidden="true"></i></span>
                        <button v-if="hasPermission('Mandat.Supprimer')" class="btn btn-danger" @click="deleteMandat(mandat)"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
              </tbody>
            </table>
        </div>
        <div class="d-flex mt-4 justify-content-center">
            <pagination
                :data="mandats"
                :limit=10
                @pagination-change-page="getMandats"
            ></pagination>
        </div>

        <!-- Modal File Mandat -->
        <modalDocument></modalDocument>
        <!-- Modal -->
        <div class="modal fade" id="addNew" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="addNew" aria-hidden="true">
            
            <div class="modal-dialog modal-xl position-relative" role="document">
                <div class="loader-line" :class="[isLoading?'d-block':'d-none']"></div>
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-uppercase" v-show="!editmode"><strong><span class="text-primary"><u>Nouveau</u></span> Mandat de Gérance</strong></h5>
                    <h5 class="modal-title" v-show="editmode"><strong><span class="text-primary"><u>Editer</u></span> Mandat de Gérance</strong></h5>
                    <button type="button" class="close" ref="closePopup" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 

                <form>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="d-block text-uppercase text-info border-bottom titleform border-info">Propriétaire</label>
                                <div class="form-group">

                                    <label>Choisir un propriétaire <span class="text-danger">*</span></label>
                                     <v-select :class="{ 'border-danger': isSubmitted && !$v.form.proprio.required }" v-model="form.proprio" :options="listProprio" :reduce="(option) => option" label="item_proprio"></v-select> 

                                    <label  class="pt-2">Choisir un bien <span class="text-danger">*</span></label>
                                     <v-select :class="{ 'border-danger': isSubmitted && !$v.form.bien.required }" v-model="form.bien" :options="biens" :reduce="(option) => option" label="item_bien"></v-select>  

                                    <label class="pt-2">Représentant </label> 

                                     <v-select v-model="form.position" :options="representants_proprio" :reduce="(option) => option" label="item_bien"></v-select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="d-block text-uppercase text-warning border-bottom titleform border-warning">Mandataire</label>
                                 <div class="form-group">

                                    <label>Choisir un mandataire <span class="text-danger">*</span></label> 
                                    <v-select  v-model="form.agence" :class="{ 'border-danger': isSubmitted && !$v.form.agence.required }" :options="currentAgence" :reduce="(option) => option" label="item_agence"></v-select> 

                                    <label class="pt-2">Représenté(e) par <span class="text-danger">*</span></label>
                                    <v-select :class="{ 'border-danger': isSubmitted && !$v.form.pers.required }" v-model="form.pers" :options="listPersonnel" :reduce="(option) => option" label="item_data"></v-select> 
                                    
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="d-block text-uppercase text-danger border-bottom titleform border-danger">Détails Mandat</label>
                            </div>
                        </div>
                        <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Durée</label>
                                        <div class="d-flex align-items-center">
                                            <input v-model="form.duree" type="number"
                                            class="form-control">
                                            <span class="px-2">An(s)</span>
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label title="Les honoraires de gestion sur les sommes collectées pour chaque mois">Honoraires de Gestion</label>
                                        <div class="d-flex align-items-center">
                                            <input v-model="form.honoraire_gestion" type="number"
                                            class="form-control">
                                            <span class="px-2">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                         <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Préavis Résiliation Propriétaire</label>
                                    <div class="d-flex align-items-center">
                                        <input v-model="form.preavis_proprio" type="number"
                                            class="form-control">
                                            <span class="px-2">Mois</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                               <div class="form-group">
                                <label>Préavis Résiliation Mandataire</label>
                                     <div class="d-flex align-items-center">
                                        <input v-model="form.preavis_mandataire" type="number"
                                            class="form-control">
                                            <span class="px-2">Mois</span>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                               <div class="form-group">
                                    <label>Date Début</label>
                                    <date-picker v-model="form.date_debut" class="w-100"  required valueType="YYYY-MM-DD" input-class="form-control w-100" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>
                                </div>
                            </div>
                            <div class="col-md-6">
                               <div class="form-group">
                                    <label>Date Fin</label>
                                    <date-picker v-model="form.date_fin" class="w-100"  required valueType="YYYY-MM-DD" input-class="form-control w-100" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>
                                </div>
                            </div>
                        </div>
                          
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button v-show="editmode" type="submit" class="btn btn-success">Enregister</button>
                        <button v-show="editmode" type="button" class="btn btn-warning"  data-dismiss="modal" @click="reset()">Annuler</button>
                        <button v-show="!editmode" @click="previsualiser()" data-toggle="modal" data-target="#previsualitionMandat"  type="button" class="btn btn-primary" :disabled="!iscompleted">Prévisualiser</button>
                        <button  v-show="!editmode" type="button" class="btn btn-info btn" @click="reset()">Réinitialiser</button>
                        <button  v-show="!editmode" type="button" class="btn btn-secondary " @click="reset()" data-dismiss="modal">Annuler</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>

        <!-- Modal details-->
        <InfoMandat></InfoMandat>

        <!-- modal carousel -->
        <modalCarousel></modalCarousel>

        <!-- Modal Prévisualisation-->
        <ModalConfirmationMandatGerance></ModalConfirmationMandatGerance>
        
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
import InfoMandat from './Info.vue';
import ModalConfirmationMandatGerance from '../modeles/mandats_de_gerance/Default.vue';

// Pdf
import { PdfMakeWrapper, Table, QR, Img} from 'pdfmake-wrapper';

import { ITable } from 'pdfmake-wrapper/lib/interfaces'; 

import pdfFonts from "pdfmake/build/vfs_fonts";

export default {
    name: "Mandat",
    props: ["listProprio", "currentAgence", "listPersonnel"],
    components: { DatePicker, VueCountryDropdown, modalCarousel, InfoMandat, ModalConfirmationMandatGerance, modalDocument },
    data () {
        return {
            editmode: false,
            mandats : {},
            checking: false,
            form: {
                id : '',
                proprio : '',
                position: '',
                bien: '',
                agence:'',
                pers:'',
                duree: '',
                date_debut: '',
                date_fin:'',
                preavis_mandataire: '',
                preavis_proprio: '',
                honoraire_gestion: '',
                fichier: null
            },
            attachmentsFichier: [],
            info: {},
            isSubmitted: false,
            isLoading: false,
            paginate: 5,
            editFichier: [],
            isLoadingTab: false,
            mandat_previsualise: null,
            iscompleted: false,
            biens: [],
            representants_proprio: []
        }
    },
    validations: {
        form : {
            agence:       { required },
            proprio:      { required },
            pers:        { required },
            bien:        { required },
            duree:           { required }

        }
    },
    watch: {
       paginate: function(){
            this.getMandats();
       },
       'form.proprio': function(value){
            if(value){
                this.getBienByProprio(value.proprio_id);
                this.getRepresentantProprio(value.proprio_id);
            }
       },
       form:  {
            handler: function (el) {
                console.log("BIEN>>", el.bien, ">>PROP",el.proprio, '>>>>AG',el.agence,'>>>PERS', this.form.pers);
                if(this.form.agence && this.form.proprio  && this.form.bien && this.form.pers  && this.form.duree  && this.form.date_debut  && this.form.date_fin  && this.form.preavis_mandataire && this.form.preavis_proprio  && this.form.honoraire_gestion){
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
        createMandat(){

            this.isSubmitted = true;
            // stop here if form is invalid
            this.$v.form.$touch();

            if (this.$v.form.$invalid) {
                return;
            }

            const data = new FormData();

            data.append('proprio', this.form.proprio.proprio_id);
            data.append('bien', this.form.bien.bien_id);
            data.append('agence', this.form.agence.agence_id);
            data.append('position', this.form.position);
            data.append('pers', this.form.pers.pers_id);
            data.append('duree', this.form.duree);
            data.append('date_debut', this.form.date_debut);
            data.append('date_fin', this.form.date_fin);
            data.append('preavis_mandataire', this.form.preavis_mandataire);
            data.append('preavis_proprio', this.form.preavis_proprio);
            data.append('honoraire_gestion', this.form.honoraire_gestion);            

            let action = "create";

            this.isLoading = true;

     
            axios.post("/gerance/"+action, data,  {
                headers: {
                    'Content-Type': 'multipart/form-data'
                } 
            }).then(response => {
                if(response.data.code==0){
                    // Generer le mandant avec le bon ID et l'enregistrer
                    this.generateMandatWithIDCreated(response.data.id_mandat);

                    

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
        getMandats(page=1){
            this.isLoadingTab = true;
            this.checking = false;
            axios.get("/gerance/listing?paginate="+ this.paginate+'&page=' + page).then(responses => {
                console.log(responses);
                this.mandats = responses.data;
                this.isLoadingTab = false;
                this.checking = true;
            }).catch(errors => { 
                this.isLoadingTab = true;
            // react on errors.

            })
        },
        reset(){

            this.form.proprio='';
            this.form.bien='';
            this.form.agence='';
            this.form.position='';
            this.form.pers='';
            this.form.date_debut='';
            this.form.date_fin='';
            this.form.preavis_mandataire='';
            this.form.preavis_proprio='';
            this.form.duree='';
            this.form.id='';
          
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
            this.form.id=current.identifiant;
            this.form.nom=current.nom;
            this.form.prenom=current.prenom;
            this.form.civilite=current.civilite;
            this.form.date_naissance=current.date_naiss_natif;
            this.form.type_locataire=current.type_location;
            this.form.pays_naissance=current.pays_naissance;
            this.form.societe=current.societe;
            this.form.num_tva=current.num_tva;
            this.form.ninea_rc=current.ninea_rc;
            this.form.domaine_activite=current.domaine_activite;
            this.form.profession=current.profession;
            this.form.revenu_mensuel=current.revenu_mensuel;
            this.form.justificatif_revenu=current.justificatif_revenu;
            this.form.indicatif1=current.ind1;
            this.form.tel1=current.tel1;
            this.form.indicatif2=current.ind2;
            this.form.tel2=current.tel2;
            this.form.email=current.email;
            this.form.adresse=current.adresse;
            this.form.ville=current.ville;
            this.form.region=current.region;
            this.form.pays=current.pays;
            this.form.code_postal=current.cp;
            this.form.type_piece=current.type_piece;
            this.form.numero_piece=current.num_piece;
            this.form.date_expiration=current.date_expiration_natif;
            this.editPhotoPerso = current.photo_perso;
            this.editPhotoPiece = current.photo_piece;
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
                        this.getMandats(); 
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
        deleteMandat(mandat){
            Vue.swal.fire({
              title:"Suppression Mandat de Gérance ",
              text: "Attention!!! cette opération est irréversible.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#3085d6',
              confirmButtonText: 'Oui, supprimer!'
            }).then((result) => {
            if (result.isConfirmed) {
    
                axios.delete('/gerance/delete/'+mandat.identifiant).then(response => {
                    console.log(response);
                    if(response.data.code==0){
                         Vue.swal.fire(
                          'Suppression',
                          'Mandat de Gérance supprimé avec succés',
                          'success'
                        );
                        this.getMandats();  
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
             axios.get("/gerance/getbien/"+id.toString()).then(responses => {
                if(responses.data.code=='0'){

                    this.biens = responses.data.data;
                    this.biens.map(function (x){ 
                        return x.item_bien = x.bien_nom+' '+x.bien_adresse + ' ' + x.bien_ville + ' (' +x.bien_id +')';
                    });  

                }
                
              
            }).catch(errors => { 
            

            })
        },
        getRepresentantProprio(id){

            this.form.position = '';
            
            axios.get("/gerance/getRepresentantProprio/"+id.toString()).then(responses => {
                if(responses.data.code=='0'){
                    this.representants_proprio = responses.data.data;
                    this.representants_proprio.map(function (x){
                      return x.item_bien = x.repr_nom+' '+x.repr_prenom + ' (' +x.repr_tel_1 +')';;
                    });  
                }
                
              
            }).catch(errors => { 
            

            })
        },
        previsualiser(){
            EventBus.$emit('VIEW_PREV_MANDAT_DE_GERANCE', { 
                mandat: this.form
            });
        },
        previsualiser(){
            console.log(">>>> position", this.form.position);
            EventBus.$emit('VIEW_PREV_MANDAT_DE_GERANCE', { 
                mandat: this.form
            });
        },
        generateMandatWithIDCreated(id_mandat){
            this.form.id=id_mandat;
            EventBus.$emit('GENERATED_MANDAT_DE_GERANCE', { 
                mandat: this.form
            });
            var thiss = this;
            setTimeout(function(){
                thiss.getMandats(); 
            }, 500)

        },
        fileMandatGenerate(file_generate, namefile){
            const data = new FormData();

            data.append('id_mandat', this.form.id);
            data.append('file_genered', file_generate);
            data.append('name_file', namefile);
            
            let action = "create_file_mandat";

            axios.post("/gerance/"+action, data,  {
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
                      'Mandat de Gérance crée avec succés!',
                      'success'
                    );

                    //this.getMandats();

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
                title: 'Mandat de Gérance'
            });
        }

    },
    mounted() {
        this.getMandats(); 

        console.log(this.listProprio) ;
        this.listProprio.map(function (x){
          return x.item_proprio = x.proprio_nom + ' ' + x.proprio_prenom + ' (' +x.proprio_id +')';
        });  

        this.currentAgence.map(function (x){
          return x.item_agence = x.agence_nom + ' (' +x.agence_id +')';
        });   

        console.log(this.listPersonnel);

        this.listPersonnel.map(function (x){
          return x.item_data = x.pers_nom + ' ' + x.pers_prenom + ' (' +x.pers_id +')';
        });  

         this.biens.map(function (x){
          return x.item_bien = x.biens_id;
        });   

        EventBus.$on('SAVE_MANDAT', (event) => {

            this.createMandat(event.file_genered, event.name_file);
           
        });  
        
        EventBus.$on('SAVE_FILE_MANDAT', (event) => {

            this.fileMandatGenerate(event.file_genered_save, event.name_file_save);
           
        });  
        
    }
}
</script>
