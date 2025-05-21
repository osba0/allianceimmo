<template>
    <div>
        <template v-if="!viewFiliale">
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
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addNew" v-on:click="newModal" ><i class="fa fa-plus-square"></i> Ajouter une Agence</button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
              <thead class="bg-white">
                <tr>
                    <th>Identifiant</th>
                    <th>Nom</th>
                    <th>Ninea</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Pays</th>
                    <th>Logo</th>
                    <th>Utilisateur</th>
                    <th class="text-right">Action</th>
                </tr>
                <tr>
                    <th colspan="9" class="position-relative p-0">
                        <div class="loader-line" :class="[isLoadingTab?'d-block':'d-none']"></div>
                    </th>
                </tr>
              </thead>
              <tbody>
                <template v-if="!agences.data || !agences.data.length">
                    <tr><td colspan="9" class="bg-white text-center" v-if="checking">Aucun résultat!</td></tr>
                </template>
                <tr v-for="agence in agences.data" :key="agence.identifiant">
                    <td class="align-middle"><h5 class="mb-0"><label class="badge badge-primary mb-0">{{agence.identifiant}}</label></h5></td>
                    <td class="align-middle"><label class="mb-0 text-primary font-weight-bold d-block">{{agence.nom_agence}}</label></td>
                    <td class="align-middle">{{agence.ninea}}</td>
                    <td class="align-middle">{{agence.email}}</td>
                    <td class="align-middle">{{agence.inde1}} {{agence.tel1}}</td>
                    <td class="align-middle">{{agence.pays}}</td>

                    <td class="align-middle">
                        <span v-if="agence.logo" class="mr-2 cursor-pointer">
                            <img :src="'/assets/agences/'+agence.logo" height="38"/>
                        </span>
                    </td>
                    <td class="align-middle">{{agence.user}}</td>
                    <td class="text-right">
                        <div class="w-100 d-flex justify-content-between">
                             <button type="button" class="btn btn-success font-weight-bold position-relative" @click="setUpFiliale(agence)">
                              Filiale <span class="badge border border-success badge-light position-absolute total-right-corner">{{agence.totalFialiale}}</span>
                            </button>
                            <div class="d-flex">
                                <button class="btn btn-primary ml-1" @click="view(agence)" data-toggle="modal" data-target="#moreInfo" v-on:click="newModal"><i class="fa fa-eye"></i></button>
                                <button class="btn btn-info ml-1" data-toggle="modal" data-target="#addNew"  v-on:click="edit(agence)"><i class="fa fa-edit"></i></button>
                                <button class="btn btn-danger ml-1" @click="deleteAgence(agence)"><i class="fa fa-trash"></i></button>
                            </div>
                        </div>


                    </td>
                </tr>
              </tbody>
            </table>
        </div>
         <div class="d-flex mt-4 justify-content-center">
            <pagination
                :data="agences"
                :limit=10
                @pagination-change-page="getAgence"
            ></pagination>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="addNew" aria-hidden="true">

            <div class="modal-dialog modal-xl position-relative" role="document">
                <div class="loader-line" :class="[isLoading?'d-block':'d-none']"></div>
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-uppercase" v-show="!editmode"><strong><span class="text-primary"><u>Nouvelle</u></span> Agence</strong></h5>
                    <h5 class="modal-title" v-show="editmode"><strong><span class="text-primary"><u>Editer</u></span> Agence</strong></h5>
                    <button type="button" class="close" ref="closePopup" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="createAgence()">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nom de l'agence <span class="required">*</span></label>
                                     <input v-model="form.nom" type="text"
                                        class="form-control" :class="{ 'border-danger': isSubmitted && !$v.form.nom.required }">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Slogan de l'agence </label>
                                     <input v-model="form.slogan" type="text"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Acitivité</label>
                                 <select class="form-control form-control-sm" v-model="form.activite">
                                    <option value=""></option>
                                    <option value="Gestion immobiliére">Gestion immobiliére</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Ninea <span class="required">*</span></label>
                                <input v-model="form.ninea" type="text"
                                    class="form-control" :class="{ 'border-danger': isSubmitted && !$v.form.ninea.required }">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email <span class="required">*</span></label>
                                    <input v-model="form.email" type="email"
                                    class="form-control"  :class="{ 'border-danger': isSubmitted && !$v.form.email.required }"/>

                                </div>
                            </div>
                            <div class="col-md-6">
                               <div class="form-group">
                                    <label>Adresse <span class="required">*</span></label>
                                    <input v-model="form.adresse" type="text"
                                        class="form-control" :class="{ 'border-danger': isSubmitted && !$v.form.adresse.required }">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                               <div class="form-group">
                                    <label>Ville <span class="required">*</span></label>
                                    <input v-model="form.ville" type="text"
                                        class="form-control" :class="{ 'border-danger': isSubmitted && !$v.form.ville.required }">

                                </div>
                            </div>
                            <div class="col-md-6">
                               <div class="form-group max-country">
                                    <label>Pays <span class="required">*</span></label>
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
                                <label>Téléphone Mobile <span class="text-danger">*</span></label>
                                <div class="d-flex">
                                    <vue-country-dropdown
                                        @onSelect="onSelectIndicatif1" :onlyCountries="['SN']" :showNameInput="false" :enabledCountryCode="true">
                                    </vue-country-dropdown>
                                    <input v-model="form.tel1" type="text"
                                    class="form-control ml-2" :class="{ 'border-danger': isSubmitted && !$v.form.tel1.required }">
                                </div>
                            </div>
                            <div class="col-md-6">
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
                         <div class="row">
                            <div class="col-md-6">
                                <label>Couleur Primaire <span class="text-danger">*</span></label>
                                <input v-model="form.colorPrimary" type="color"
                                    class="form-control px-0 ml-2" :class="{ 'border-danger': isSubmitted && !$v.form.colorPrimary.required }">
                            </div>
                            <div class="col-md-6">
                                <label>Couleur Secondaire</label>
                                <input v-model="form.colorSecondary" type="color"
                                    class="form-control px-0 ml-2" :class="{ 'border-danger': isSubmitted && !$v.form.colorSecondary.required }">
                            </div>
                        </div>
                         <div class="row mt-2">
                            <div class="col-md-12">
                               <div class="form-group">
                                    <label>Logo</label>
                                    <input name="file" type="file" ref="attachmentsPhotos"
                                        class="form-control border-0 pl-0" v-on:change="handleFileUpload()">
                                </div>
                                <div v-if="editmode">
                                     <span v-for="photo in editKyc" class="mr-3 cursor-pointer" v-on:click="supprimerPhoto(photo, editKyc, form.id)">
                                        <img :src="'/assets/agences/'+photo" height="50" data-toggle="modal" data-target="#carouselPhoto" @click="setKyc(pro)"/>
                                        <i class="text-danger fa fa-times"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer justify-content-center">
                        <button v-show="editmode" type="submit" class="btn btn-success">Enregister</button>
                        <button v-show="editmode" type="button" class="btn btn-warning"  data-dismiss="modal" @click="reset()">Annuler</button>
                        <button v-show="!editmode" type="submit" class="btn btn-success">Créer</button>
                        <button  v-show="!editmode" type="button" class="btn btn-info" @click="reset()">Réinitialiser</button>
                        <button  v-show="!editmode" type="button" class="btn btn-secondary" @click="reset()" data-dismiss="modal">Annuler</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>

        <!-- modal info -->
        <Info></Info>
    </template>
    <template v-else>
        <FilialeSetup></FilialeSetup>
    </template>
    </div>
</template>

<script>
import DatePicker from 'vue2-datepicker';
import VueCountryDropdown from 'vue-country-dropdown';
import 'vue2-datepicker/index.css';
import modalCarousel from '../../components/modal/carousel.vue';
import FilialeSetup from './Filiale.vue';
import Info from './Info.vue';
import { EventBus } from "../../event-bus"; 
import { required, email, minLength, between, minValue  } from 'vuelidate/lib/validators';

export default {
    name: "Agence",
    props: ["listAgences"],
    components: { DatePicker, VueCountryDropdown, FilialeSetup, Info },
    data () {
        return {
            checking: false,
            editmode: false,
            agences : {},
            form: {
                ninea: '',
                slogan: '',
                id : '',
                nom: '',
                adresse: '',
                ville: '',
                pays: '',
                logo: null,
                favicon: null,
                activite: '',
                indicatif1: '',
                indicatif2: '',
                tel1: '',
                tel2: '',
                colorPrimary: '',
                colorSecondary: ''
            },
            attachmentsPhotos: [],
            info: {},
            isSubmitted: false,
            isLoading: false,
            paginate: 5,
            editKyc: [],
            isLoadingTab: false,
            viewFiliale: false
        }
    },
    validations: {
        form : {
            adresse: { required },
            ville:   { required },
            pays:    { required },
            proprio: { required },
            numero:  { required },
            activite: { required },
            tel1: { required },
            tel2: { required },
            colorPrimary: { required },
            colorSecondary: { required }
        }
    },
    watch: {
       paginate: function(){
            this.getAgence();
       }
    },
    methods: {
        createAgence(){

            this.isSubmitted = true;
            // stop here if form is invalid
            this.$v.form.$touch();

            if (this.$v.form.$invalid) {
                return;
            }

            const data = new FormData();
            data.append('nom', this.form.nom);
            data.append('slogan', this.form.slogan);
            data.append('adresse', this.form.adresse);
            data.append('ind1', this.form.indicatif1);
            data.append('ind2', this.form.indicatif2);
            data.append('numero', this.form.numero);
            data.append('ville', this.form.ville);
            data.append('pays', this.form.pays);
            data.append('tel1', this.form.tel1);
            data.append('tel2', this.form.tel2);
            data.append('activite', this.form.activite);
            data.append('colorPrimary', this.form.colorPrimary);
            data.append('colorSecondary', this.form.colorSecondary);


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

     
            axios.post("/entite/"+action, data,  {
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
                      'Agence crée avec succés!',
                      'success'
                    );

                    this.getAgence();

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
        getAgence(page=1){
            this.isLoadingTab = true;
            axios.get("/entite/listing?paginate="+ this.paginate+'&page=' + page).then(responses => {
              console.log(responses);
              this.agences = responses.data;
              this.isLoadingTab = false;
              this.checking = true;
            }).catch(errors => { 

            // react on errors.

            })
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
                path: '/assets/agences/'
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
                        this.getAgence
                        ();
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
        deleteAgence(item){
            Vue.swal.fire({
              title:"Suppression Agence ",
              text: "Attention!!! cette opération est irréversible.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#3085d6',
              confirmButtonText: 'Oui, supprimer!'
            }).then((result) => {
            if (result.isConfirmed) {
    
                axios.delete('/entite/delete/'+item.identifiant).then(response => {
                    console.log(response);
                    if(response.data.code==0){
                         Vue.swal.fire(
                          'Suppression',
                          'Bien supprimé avec succés',
                          'success'
                        );
                        this.getAgence();
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
        setUpFiliale(agence){

            this.viewFiliale = true;

            setTimeout(function(){
                EventBus.$emit('VIEW_FILIALE', {
                    current_agence: agence
                });
            }, 200);
            
        },
       

    },
    mounted() {
        this.getAgence();
        this.listAgences.map(function (x){
          return x.item_data = x.nom_agence + ' (' +x.identifiant +')';
        });
        EventBus.$on('BACK', (event) => {
          this.viewFiliale = event.back;
          this.getAgence();
        });    
    }
}
</script>
