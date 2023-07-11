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
            <button type="button" class="btn btn-lg btn-primary" data-toggle="modal" data-target="#addNew" v-on:click="newModal" ><i class="fa fa-plus-square"></i> Nouveau Locataire</button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
              <thead class="bg-white">
                <tr>
                    <th>Identifiant</th>
                    <th>Nom & Prénom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Adresse</th>
                    <th>Piéce</th>
                    <th class="nowrap">Photo piéce</th>
                    <th class="text-right">Action</th>
                </tr>
                <tr>
                    <th colspan="9" class="position-relative p-0">
                        <div class="loader-line" :class="[isLoadingTab?'d-block':'d-none']"></div>
                    </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="locataire in locataires.data" :key="locataire.identifiant">
                    <td class="align-middle"><h5 class="mb-0"><label class="badge badge-primary mb-0">{{locataire.identifiant}}</label></h5></td>
                    <td class="align-middle">
                        <div class="d-flex">
                            <span v-for="photo in locataire.photo_perso" class="mr-2 cursor-pointer">
                                <img :src="'/assets/locataires/'+photo" height="38" data-toggle="modal" data-target="#carouselPhoto" @click="setKyc(locataire)"/>
                            </span>
                            <h1 v-if="locataire.photo_perso.length==0"><i class="fa fa-camera-retro" aria-hidden="true"></i></h1>
                            <span class="ml-2 mb-0">{{locataire.nom}} {{locataire.prenom}}</span>
                        </div>
                    </td>
                    <td class="align-middle">{{locataire.email}}</td>
                    <td class="align-middle">
                        <div class="nowrap">{{locataire.ind1}} {{locataire.tel1}}</div>
                    </td>
                    <td class="align-middle">{{locataire.adresse}}</td>
                    <td class="align-middle"><label class="badge badge-info">{{locataire.type_piece}}</label>&nbsp;{{locataire.num_piece}}</td>
                    <td class="align-middle">
                        <span v-for="photo in locataire.photo_piece" class="mr-2 cursor-pointer">
                            <img :src="'/assets/locataires/'+photo" height="38" data-toggle="modal" data-target="#carouselPhoto" @click="setKyc(locataire)"/>
                        </span>
                        <h1 v-if="locataire.photo_piece.length==0"><i class="fa fa-camera-retro" aria-hidden="true"></i></h1>
                    </td>
                    <td class="text-right align-middle">
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary" @click="view(locataire)" data-toggle="modal" data-target="#moreInfo" v-on:click="newModal"><i class="fa fa-eye"></i></button>
                            <button class="btn btn-info mx-2" data-toggle="modal" data-target="#addNew"  v-on:click="edit(locataire)"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-danger" @click="deleteProprio(locataire)"><i class="fa fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
              </tbody>
            </table>
        </div>
         <div class="d-flex mt-4 justify-content-center">
            <pagination
                :data="locataires"
                :limit=10
                @pagination-change-page="getLocataire"
            ></pagination>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="addNew" aria-hidden="true">
            
            <div class="modal-dialog modal-xl position-relative" role="document">
                <div class="loader-line" :class="[isLoading?'d-block':'d-none']"></div>
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-uppercase" v-show="!editmode"><strong><span class="text-primary"><u>Nouveau</u></span> Locataire</strong></h5>
                    <h5 class="modal-title" v-show="editmode"><strong><span class="text-primary"><u>Editer</u></span> Locataire</strong></h5>
                    <button type="button" class="close" ref="closePopup" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 

                <form @submit.prevent="createLocataire()">
                    <div class="modal-body">
                        <div class="d-flex">
                            <div class="orientation mr-3 text-left border-right pr-2">
                                <label class="text-uppercase text-info titleform border-info pt-2">Infos Perso / Infos Société</label>
                            </div>
                            <div class="flex-grow-1">
                                <div class="row">
                                    <div class="col-md-6 d-flex justify-content-between">
                                        <div class="form-group w-49">
                                            <label>Civilité <span class="text-danger">*</span></label>
                                            <div class="d-flex align-items-center mt-2">
                                                <input id="Mlle" v-model="form.civilite" value="Mlle" type="radio">
                                                <label class="pr-3 mb-0 pl-1" for="Mlle">M<sup>lle</sup></label> 
                                                <input id="Mme" v-model="form.civilite" value="Mme" type="radio">
                                                <label class="pr-3 mb-0 pl-1" for="Mme">M<sup>me</sup></label> 
                                                <input id="M" v-model="form.civilite" value="M." type="radio">
                                                <label class="pr-3 mb-0 pl-1" for="M">M.</label>  
                                            </div>
                                           
                                        </div>
                                        <div class="form-group w-49">
                                            <label>Type Locataire <span class="text-danger">*</span></label>
                                             <select class="form-control" v-model="form.type_locataire" :class="{ 'border-danger': isSubmitted && !$v.form.type_locataire.required }">
                                                    <option value="">Choisir</option>
                                                    <option value="particulier">Particulier</option>
                                                    <option value="société">Société</option>
                                                    <option value="autre">Autre</option>
                                                </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-between">
                                       <div class="form-group w-49">
                                            <label>Nom <span class="text-danger">*</span></label>
                                            <input v-model="form.nom" type="text"
                                                class="form-control" :class="{ 'border-danger': isSubmitted && !$v.form.nom.required }">
                                        </div>
                                         <div class="form-group max-country  w-49">
                                            <label>Prénom <span class="text-danger">*</span></label>
                                            <input v-model="form.prenom" type="text"
                                                class="form-control" :class="{ 'border-danger': isSubmitted && !$v.form.prenom.required }">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 d-flex justify-content-between">
                                        <div class="form-group w-49">
                                             <label>Date de naissance</label>
                                              <date-picker v-model="form.date_naissance" class="w-100"  valueType="YYYY-MM-DD" input-class="form-control w-100" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>
                                            
                                        </div>
                                        <div class="form-group w-49 max-country"> 
                                            <label>Pays de naissance</label>
                                            <vue-country-dropdown
                                                    @onSelect="onSelectNaissance" :onlyCountries="['SN']" :showNameInput="true">
                                                </vue-country-dropdown>
                                           
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-between">
                                       <div class="form-group w-49">
                                            <label>Société</label>
                                            <input v-model="form.societe" type="text"
                                                class="form-control">
                                        </div>
                                         <div class="form-group max-country  w-49">
                                            <label>N° TVA</label>
                                            <input v-model="form.num_tva" type="text"
                                                class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 d-flex justify-content-between">
                                       <div class="form-group w-49">
                                            <label>Ninea / Registre Commerce</label>
                                            <input v-model="form.ninea_rc" type="text"
                                                class="form-control">
                                        </div>
                                         <div class="form-group max-country  w-49">
                                            <label>Domaine d'Activité</label>
                                            <input v-model="form.domaine_activite" type="text"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                            <label>Photo</label>
                                            <input name="file" multiple type="file" ref="attachmentsPhotosPerso" 
                                                class="form-control border-0 pl-0" v-on:change="handleFileUploadPerso()">
                                        </div>
                                        <div v-if="editmode">
                                             <span v-for="photo in editPhotoPerso" class="mr-3 cursor-pointer" v-on:click="supprimerPhoto(photo, editPhotoPerso, form.id, 'perso')">
                                                <img :src="'/assets/locataires/'+photo" height="50" data-toggle="modal" data-target="#carouselPhoto" @click="setKyc(pro)"/>
                                                <i class="text-danger fa fa-times"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 d-flex justify-content-between">
                                       <div class="form-group w-49">
                                            <label>Profession <span class="text-danger">*</span></label>
                                            <input v-model="form.profession" type="text"
                                                class="form-control"  :class="{ 'border-danger': isSubmitted && !$v.form.profession.required }">
                                           
                                        </div>
                                         <div class="form-group w-49">
                                            <label>Revenus mensuels</label>
                                            <input v-model="form.revenu_mensuel" type="text"
                                                class="form-control">
                                           
                                        </div>
                                    </div>
                                     <div class="col-md-6">
                                       <div class="form-group">
                                            <label>Justificatif de Revenus</label>
                                            <input v-model="form.justificatif_revenu" type="text"
                                                class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="orientation mr-3 text-left border-right pr-2">
                                <label class="text-uppercase text-info titleform border-info pt-2">Info Contact</label>
                            </div>
                            <div class="flex-grow-1">
                                <div class="row">
                                    <div class="col-md-6 d-flex justify-content-between">
                                       <div class="form-group w-49">
                                            <label>Téléphone Mobile <span class="text-danger">*</span></label>
                                            <div class="d-flex">
                                                <vue-country-dropdown
                                                    @onSelect="onSelectIndicatif1" :onlyCountries="['SN']" :showNameInput="false" :enabledCountryCode="true">
                                                </vue-country-dropdown>
                                                <input v-model="form.tel1" type="text"
                                                class="form-control ml-2" :class="{ 'border-danger': isSubmitted && !$v.form.tel1.required }">
                                            </div>
                                            
                                        </div>
                                         <div class="form-group  w-49">
                                            <label>Téléphone Fixe</label>
                                            <div class="d-flex">
                                                <vue-country-dropdown
                                                        @onSelect="onSelectIndicatif2" :onlyCountries="['SN']" :showNameInput="false" :enabledCountryCode="true">
                                                </vue-country-dropdown>
                                                <input v-model="form.tel2" type="text"
                                                    class="form-control ml-2">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-between">
                                       <div class="form-group w-49">
                                            <label>Email <span class="text-danger">*</span></label>
                                            <input v-model="form.email" type="text"
                                                class="form-control" :class="{ 'border-danger': isSubmitted && !$v.form.email.required }">
                                           
                                        </div>
                                         <div class="form-group w-49">
                                            <label>Adresse</label>
                                            <input v-model="form.adresse" type="text" class="form-control">
                                           
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 d-flex justify-content-between">
                                       <div class="form-group w-49">
                                            <label>Ville</label>
                                            <input v-model="form.ville" type="text"
                                                class="form-control">
                                            
                                        </div>
                                         <div class="form-group  w-49">
                                            <label>Région</label>
                                            <input v-model="form.region" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-between">
                                       <div class="form-group w-49">
                                            <label>Code Postal</label>
                                            <input v-model="form.code_postal" type="text" class="form-control">
                                           
                                        </div>
                                         <div class="form-group w-49 max-country">
                                            <label>Pays</label>
                                             <vue-country-dropdown
                                                    @onSelect="onSelectResidance" :onlyCountries="['SN']" :showNameInput="true">
                                                </vue-country-dropdown>
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="orientation mr-3 text-left border-right pr-2">
                                <label class="text-uppercase text-info titleform border-info pt-2">Piéce d'Identité</label>
                            </div>
                            <div class="flex-grow-1">
                                <div class="row">
                                    <div class="col-md-6">
                                       <div class="form-group">
                                            <label>Type piéce <span class="text-danger">*</span></label>
                                            <select v-model="form.type_piece" class="form-control" :class="{ 'border-danger': isSubmitted && !$v.form.type_piece.required }">
                                                <option value="">Choisir</option>
                                                <option value="CNI">CNI</option>
                                                <option value="PASSEPORT">Passeport</option>
                                                <option value="PERMIS">Permis</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                       <div class="form-group">
                                            <label>N°Piéce</label>
                                            <input v-model="form.numero_piece" type="text"
                                                class="form-control">
                                           
                                        </div>
                                    </div>
                                </div>
                                 <div class="row">
                                    <div class="col-md-6">
                                       <div class="form-group d-flex justify-content-between flex-column">
                                            <label>Date expiration piéce</label>
                                            <date-picker v-model="form.date_expiration" class="w-100"  valueType="YYYY-MM-DD" input-class="form-control w-100" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                       <div class="form-group">
                                            <label>Photo piéce</label>
                                            <input name="file" multiple type="file" ref="attachmentsPhotos" 
                                                class="form-control border-0 pl-0" v-on:change="handleFileUpload()">
                                        </div>
                                        <div v-if="editmode">
                                             <span v-for="photo in editPhotoPiece" class="mr-3 cursor-pointer" v-on:click="supprimerPhoto(photo, editPhotoPiece, form.id, 'piece')">
                                                <img :src="'/assets/locataires/'+photo" height="50" data-toggle="modal" data-target="#carouselPhoto" @click="setKyc(pro)"/>
                                                <i class="text-danger fa fa-times"></i>
                                            </span>
                                        </div>
                                    </div>
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

        <!-- Modal details-->
        <InfoLocataire></InfoLocataire>

        <!-- modal carousel -->
        <modalCarousel></modalCarousel>
        
    </div>
</template>

<script>
import DatePicker from 'vue2-datepicker';
import VueCountryDropdown from 'vue-country-dropdown';
import 'vue2-datepicker/index.css';
import modalCarousel from '../../components/modal/carousel.vue';
import { EventBus } from "../../event-bus"; 
import { required, email, minLength, between } from 'vuelidate/lib/validators';
import InfoLocataire from './Info.vue';
export default {
    name: "Proprietaire",
    props: [],
    components: { DatePicker, VueCountryDropdown, modalCarousel, InfoLocataire },
    data () {
        return {
            editmode: false,
            locataires : {},
            form: {
                id : '',
                nom : '',
                prenom: '',
                civilite:'',
                date_naissance: '',
                type_locataire:'',
                pays_naissance: '',
                societe: '',
                num_tva: '',
                ninea_rc: '',
                domaine_activite: '',
                photo_perso: null,
                profession: '',
                revenu_mensuel: '',
                justificatif_revenu: '',
                indicatif1: '',
                tel1: '',
                indicatif2: '',
                tel2:  '',
                email: '',
                adresse: '',
                ville: '',
                region: '',
                pays: '',
                code_postal: '',
                type_piece: '',
                numero_piece: '',
                date_expiration:'',
                photo_piece: null
            },
            attachmentsPhotos: [],
            attachmentsPhotosPerso: [],
            info: {},
            isSubmitted: false,
            isLoading: false,
            paginate: 5,
            editPhotoPerso: [],
            editPhotoPiece:[],
            isLoadingTab: false
        }
    },
    validations: {
        form : {
            nom:            { required },
            prenom:         { required },
            tel1:           { required },
            email:          { email, required },
            adresse:        { required },
            civilite:       { required },
            type_piece:     { required },
            type_locataire: { required },
            profession:     { required }
        }
    },
    watch: {
       paginate: function(){
            this.getLocataire();
       }
    },
    methods: {
        createLocataire(){

            this.isSubmitted = true;
            // stop here if form is invalid
            this.$v.form.$touch();

            if (this.$v.form.$invalid) {
                return;
            }

            const data = new FormData();
            data.append('nom', this.form.nom);
            data.append('prenom', this.form.prenom);
            data.append('email', this.form.email);
            data.append('type_location', this.form.type_locataire);
            data.append('pays_naissance', this.form.pays_naissance);
            data.append('date_naissance', this.form.date_naissance);
            data.append('cp', this.form.code_postal);
            data.append('societe', this.form.societe);
            data.append('num_tva', this.form.num_tva);
            data.append('ninea_rc', this.form.ninea_rc);
            data.append('domaine_activite', this.form.domaine_activite);
            data.append('profession', this.form.profession);
            data.append('revenu_mensuel', this.form.revenu_mensuel);
            data.append('justificatif_revenu', this.form.justificatif_revenu);
            data.append('indicatif1', this.form.indicatif1);
            data.append('tel1', this.form.tel1); 
            data.append('indicatif2', this.form.indicatif2);
            data.append('tel2', this.form.tel2);
            data.append('adresse', this.form.adresse);
            data.append('ville', this.form.ville);
            data.append('pays', this.form.pays);
            data.append('civilite', this.form.civilite);
            data.append('type_piece', this.form.type_piece);
            data.append('num_piece', this.form.numero_piece);
            data.append('date_expiration', this.form.date_expiration);
            data.append('region', this.form.region);
            data.append('file[]', this.attachmentsPhotos);

            for (let i = 0; i < this.attachmentsPhotos.length; i++) {
                data.append('files' + i, this.attachmentsPhotos[i]);
            }
             for (let i = 0; i < this.attachmentsPhotosPerso.length; i++) {
                data.append('filesPerso' + i, this.attachmentsPhotosPerso[i]);
            }
            data.append('TotalFiles', this.attachmentsPhotos.length);
            data.append('TotalFilesPerso', this.attachmentsPhotosPerso.length);

            let action = "create";

            if(this.editmode){
                data.append('additionalFilePiece', JSON.stringify(this.editPhotoPiece));
                data.append('additionalFilePerso', JSON.stringify(this.editPhotoPerso));
                action = "modify/"+this.form.id;
            }

            this.isLoading = true;

     
            axios.post("/locat/"+action, data,  { 
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
                      'Locataire crée avec succés!',
                      'success'
                    );

                    this.getLocataire();    

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
        onSelectIndicatif1({name, iso2, dialCode}) {
           this.form.indicatif1 = dialCode; 
        },
        onSelectIndicatif2({name, iso2, dialCode}) {
           this.form.indicatif2 = dialCode; 
        },
        newModal(){
          this.editmode = false;
        },
        getLocataire(page=1){
            this.isLoadingTab = true;
            axios.get("/locat/listing?paginate="+ this.paginate+'&page=' + page).then(responses => {
                console.log(responses);
                this.locataires = responses.data;
                this.isLoadingTab = false;
            }).catch(errors => { 
                this.isLoadingTab = true;
            // react on errors.

            })
        },
        reset(){
            this.form.nom='';
            this.form.prenom='';
            this.form.civilite='';
            this.form.date_naissance='';
            this.form.type_locataire='';
            this.form.pays_naissance='';
            this.societe='';
            this.form.num_tva='';
            this.form.ninea_rc='';
            this.form.domaine_activite='';
            this.form.photo_perso=null;
            this.form.profession='';
            this.form.revenu_mensuel='';
            this.form.justificatif_revenu='';
            this.form.indicatif1='';
            this.form.tel1='';
            this.form.indicatif2='';
            this.form.tel2='';
            this.form.email='';
            this.form.adresse='';
            this.form.ville='';
            this.form.region='';
            this.form.pays='';
            this.form.code_postal='';
            this.type_piece='';
            this.form.numero_piece='';
            this.form.date_expiration='';
            this.form.photo_piece= null;
            this.attachmentsPhotos= [];
            this.attachmentsPhotosPerso= [];
            this.$refs.attachmentsPhotosPerso.value = null
            this.$refs.attachmentsPhotos.value = null
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
                kyc: prop.photo_piece,
                path: '/assets/locataires/'
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
                        this.getLocataire(); 
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
              title:"Suppression Propriétaire ",
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
                          'Propriétaire supprimé avec succés',
                          'success'
                        );
                        this.getLocataire();  
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
        }

    },
    mounted() {
        this.getLocataire();          
    }
}
</script>
