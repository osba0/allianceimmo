<template>
    <div>
        <template v-if="!viewLocal">
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
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addNew" v-on:click="newModal" ><i class="fa fa-plus-square"></i> Ajouter un Immeuble</button>
            </div>
             
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                  <thead class="bg-white">
                    <tr>
                        <th>Identifiant</th>
                        <th>Propriétaire</th>
                        <th>Nom Immeuble</th>
                        <th>Ville</th>
                        <th>Pays</th>
                        <th>Etage</th>
                        <th>Superficie m<sup>2</sup></th>
                        <th>Photo</th>
                        <th class="text-right">Action</th>
                    </tr>
                    <tr>
                        <th colspan="9" class="position-relative p-0">
                            <div class="loader-line" :class="[isLoadingTab?'d-block':'d-none']"></div>
                        </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="bien in biens.data" :key="bien.identifiant">
                        <td class="align-middle"><h5 class="mb-0"><label class="badge badge-primary mb-0">{{bien.identifiant}}</label></h5></td>
                        <td class="align-middle">{{bien.proprio_nom}} {{bien.proprio_prenom}}</td>
                        <td><label class="mb-0 text-primary font-weight-bold d-block">{{bien.nom_immeuble}}</label><u>N° {{ bien.numero }}</u> {{ bien.adresse }}</td>
                        <td class="align-middle">{{bien.ville}}</td>
                        <td class="align-middle">{{bien.pays}}</td>
                        <td class="align-middle">{{bien.etage}}</td>
                        <td class="align-middle">{{bien.superficie}}</td>
                        <td class="align-middle">
                            <span v-for="photo in bien.photo" class="mr-2 cursor-pointer">
                                <img :src="'/assets/biens/'+photo" height="38" data-toggle="modal" data-target="#carouselPhoto" @click="setKyc(bien)"/>
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="w-100 d-flex justify-content-between">
                                 <button type="button" class="btn btn-success font-weight-bold position-relative" @click="setUpLocal(bien)">
                                  Local <span class="badge border border-success badge-light position-absolute total-right-corner">{{bien.totalLocal}}</span>
                                </button>
                                <div>
                                    <button class="btn btn-primary" @click="view(bien)" data-toggle="modal" data-target="#moreInfo" v-on:click="newModal"><i class="fa fa-eye"></i></button>
                                    <button class="btn btn-info" data-toggle="modal" data-target="#addNew"  v-on:click="edit(bien)"><i class="fa fa-edit"></i></button>
                                    <button class="btn btn-danger" @click="deleteBien(bien)"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>
                           
                           
                        </td>
                    </tr>
                  </tbody>
                </table>
            </div>
             <div class="d-flex mt-4 justify-content-center">
                <pagination
                    :data="biens"
                    :limit=10
                    @pagination-change-page="getBien"
                ></pagination>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="addNew" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="addNew" aria-hidden="true">
                
                <div class="modal-dialog modal-xl position-relative" role="document">
                    <div class="loader-line" :class="[isLoading?'d-block':'d-none']"></div>
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-uppercase" v-show="!editmode"><strong><span class="text-primary"><u>Nouveau</u></span> Bien / Immeuble</strong></h5>
                        <h5 class="modal-title" v-show="editmode"><strong><span class="text-primary"><u>Editer</u></span> Bien / Immeuble</strong></h5>
                        <button type="button" class="close" ref="closePopup" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 

                    <form @submit.prevent="createProprio()">
                        <div class="modal-body">
                            <!--horizontal-stepper :steps="demoSteps" @completed-step="completeStep"
                                        @active-step="isStepActive" @stepper-finished="alert">                     
                            </horizontal-stepper-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Propriétaire</label>
                                         <v-select :class="{ 'border-danger': isSubmitted && !$v.form.proprio.required }" v-model="form.proprio" :options="listProprio" :reduce="(option) => option.proprio_id"label="item_data"></v-select> 
                                    </div>
                                </div>
                                <div class="col-md-6 d-flex justify-content-between">
                                    <div class="form-group w-49">
                                        <label>Nom Immeuble</label>
                                        <input v-model="form.nom" type="text"
                                            class="form-control">
                                    </div>
                                    <div class="form-group w-49">
                                        <label>N°Immeuble</label>
                                        <input v-model="form.numero" type="text"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nombre d'Etage</label>
                                        <input v-model="form.etage" type="number"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label>Adresse</label>
                                        <input v-model="form.adresse" type="text"
                                            class="form-control" :class="{ 'border-danger': isSubmitted && !$v.form.adresse.required }">                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label>Ville</label>
                                        <input v-model="form.ville" type="text"
                                            class="form-control" :class="{ 'border-danger': isSubmitted && !$v.form.ville.required }">
                                       
                                    </div>
                                </div>
                                <div class="col-md-6">
                                   <div class="form-group max-country">
                                        <label>Pays</label>
                                        <input v-model="form.pays" type="text"
                                            class="form-control d-none"  :class="{ 'border-danger': isSubmitted && !$v.form.pays.required }">

                                            <vue-country-dropdown
                                                @onSelect="onSelect" :onlyCountries="['SN']" :showNameInput="true">
                                            </vue-country-dropdown>
                                       
                                    </div>
                                </div>
                            </div>
                           
                            <div class="row">
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label>Superficie m<sup>2</sup></label>
                                         <input v-model="form.superficie" type="text"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label>Année de construction</label>
                                          <date-picker v-model="form.annee_construction" class="w-100"  required valueType="YYYY-MM-DD" input-class="form-control w-100" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>
                                       
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-6">
                                   <div class="form-group d-flex justify-content-between flex-column">
                                        <label>Description</label>
                                        <textarea class="form-control" v-model="form.description"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label>Photos</label>
                                        <input name="file" multiple type="file" ref="attachmentsPhotos" 
                                            class="form-control border-0 pl-0" v-on:change="handleFileUpload()">
                                    </div>
                                    <div v-if="editmode">
                                         <span v-for="photo in editKyc" class="mr-3 cursor-pointer" v-on:click="supprimerPhoto(photo, editKyc, form.id)">
                                            <img :src="'/assets/biens/'+photo" height="50" data-toggle="modal" data-target="#carouselPhoto" @click="setKyc(pro)"/>
                                            <i class="text-danger fa fa-times"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                              
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button v-show="editmode" type="submit" class="btn btn-lg btn-success">Enregister</button>
                            <button v-show="editmode" type="button" class="btn btn-lg btn-warning"  data-dismiss="modal" @click="reset()">Annuler</button>
                            <button v-show="!editmode" type="submit" class="btn btn-lg btn-success">Créer</button>
                            <button  v-show="!editmode" type="button" class="btn btn-info btn btn-lg" @click="reset()">Réinitialiser</button>
                            <button  v-show="!editmode" type="button" class="btn btn-secondary btn btn-lg" @click="reset()" data-dismiss="modal">Annuler</button>
                        </div>
                      </form>
                    </div>
                </div>
            </div>

            <!-- modal info -->
            <Info></Info>
            <!-- modal carousel -->
            <modalCarousel></modalCarousel>
        </template>
        <template v-else>
            
            <LocalSetup></LocalSetup>
        </template>
        
        
    </div>
</template>

<script>
import DatePicker from 'vue2-datepicker';
import VueCountryDropdown from 'vue-country-dropdown';
import 'vue2-datepicker/index.css';
import modalCarousel from '../../components/modal/carousel.vue';
import { EventBus } from "../../event-bus"; 
import { required, email, minLength, between } from 'vuelidate/lib/validators';
import HorizontalStepper from 'vue-stepper';
import Info from './Info.vue';
import LocalSetup from './Local.vue';

export default {
    name: "Proprietaire",
    props: ["listProprio"],
    components: { DatePicker, VueCountryDropdown, modalCarousel, HorizontalStepper, Info, LocalSetup },
    data () {
        return {
            editmode: false,
            biens : {},
            form: {
                proprio: '',
                id : '',
                nom: '',
                adresse: '',
                ville: '',
                pays: '',
                superficie:'',
                description: '',
                annee_construction: '',
                numero:'',
                etage:'',
                photo_piece: null
            },
            attachmentsPhotos: [],
            info: {},
            isSubmitted: false,
            isLoading: false,
            paginate: 5,
            editKyc: [],
            isLoadingTab: false,
            viewLocal: false
        }
    },
    validations: {
        form : {
            adresse: { required },
            ville:   { required },
            pays:    { required },
            proprio: { required }
        }
    },
    watch: {
       paginate: function(){
            this.getBien();
       }
    },
    methods: {
        createProprio(){

            this.isSubmitted = true;
            // stop here if form is invalid
            this.$v.form.$touch();

            if (this.$v.form.$invalid) {
                return;
            }

            const data = new FormData();
            data.append('proprio', this.form.proprio);
            data.append('adresse', this.form.adresse);
            data.append('etage', this.form.etage);
            data.append('nom', this.form.nom);
            data.append('numero', this.form.numero);
            data.append('ville', this.form.ville);
            data.append('pays', this.form.pays);
            data.append('superficie', this.form.superficie);
            data.append('description', this.form.description);
            data.append('annee_construction', this.form.annee_construction);
            data.append('file[]', this.attachmentsPhotos);

            for (let i = 0; i < this.attachmentsPhotos.length; i++) {
                data.append('files' + i, this.attachmentsPhotos[i]);
            }
            data.append('TotalFiles', this.attachmentsPhotos.length);

            let action = "create";

            if(this.editmode){
                data.append('additionalFile', JSON.stringify(this.editKyc));
                action = "modify/"+this.form.id;
            }

            this.isLoading = true;

     
            axios.post("/bien/"+action, data,  {
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
                      'Bien crée avec succés!',
                      'success'
                    );

                    this.getBien();

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
        newModal(){
          this.editmode = false;
        },
        getBien(page=1){
            this.isLoadingTab = true;
            axios.get("/bien/listing?paginate="+ this.paginate+'&page=' + page).then(responses => {
              console.log(responses);
              this.biens = responses.data;
               this.isLoadingTab = false;
            }).catch(errors => { 

            // react on errors.

            })
        },
        reset(){
            this.form.id = '';
            this.form.identifiant = '';
            this.form.adresse = '';
            this.form.nom = '';
            this.form.superficie = '';
            this.form.annee_construction = '';
            this.form.ville = '';
            this.form.pays = '';
            this.form.description = '';
            this.form.etage = '';
            this.form.numero = '';
            this.attachmentsPhotos= [];
            this.$refs.attachmentsPhotos.value = null
        },
        handleFileUpload(){
            this.attachmentsPhotos = [];
            for(var i=0; i<this.$refs.attachmentsPhotos.files.length;i++){
                this.attachmentsPhotos.push(this.$refs.attachmentsPhotos.files[i])
            }
        },
        edit(current){
            this.editmode = true;
            this.form.id = current.identifiant;
            this.form.proprio = current.proprio;
            this.form.nom = current.nom_immeuble;
            this.form.adresse = current.adresse;
            this.form.superficie = current.superficie;
            this.form.ville = current.ville;
            this.form.pays = current.pays;
            this.form.description = current.description;
            this.form.annee_construction = current.annee_cons_natif;
            this.form.etage = current.etage;
            this.form.numero = current.numero;
            this.editKyc = current.photo;
        },
        setKyc(prop){
             // get photo
            EventBus.$emit('VIEW_CAROUSEL', { 
                kyc: prop.photo,
                path: '/assets/biens/'
            });
        },
        view(item){
             EventBus.$emit('VIEW_INFO', { 
                info:  item
            });
        },
        supprimerPhoto(name, kyc, id){
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
                data.append('kyc', JSON.stringify(kyc));
                data.append('namePhoto', name);
                axios.post('/bien/remphoto', data).then(response => {
                    console.log(response);
                    if(response.data.code==0){
                         Vue.swal.fire(
                          'Suppression',
                          'Photo supprimée avec succés',
                          'success'
                        );
                        this.editKyc = response.data.file;
                        this.getBien();  
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
        deleteBien(item){
            Vue.swal.fire({
              title:"Suppression Bien ",
              text: "Attention!!! cette opération est irréversible.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#3085d6',
              confirmButtonText: 'Oui, supprimer!'
            }).then((result) => {
            if (result.isConfirmed) {
    
                axios.delete('/bien/delete/'+item.identifiant).then(response => {
                    console.log(response);
                    if(response.data.code==0){
                         Vue.swal.fire(
                          'Suppression',
                          'Bien supprimé avec succés',
                          'success'
                        );
                        this.getBien();  
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
        setUpLocal(bien){

            this.viewLocal = true;

            setTimeout(function(){
                EventBus.$emit('VIEW_LOCAL', { 
                    current: bien
                });
            }, 200);
            
        },
       

    },
    mounted() {
        this.getBien();  
        this.listProprio.map(function (x){
          return x.item_data = x.proprio_nom + ' ' + x.proprio_prenom + ' (' +x.proprio_id +')';
        });
        EventBus.$on('BACK', (event) => {
          this.viewLocal = event.back;
        });    
    }
}
</script>
