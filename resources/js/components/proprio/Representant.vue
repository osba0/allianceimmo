<template>
    <div>
         <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex">
                <button type="button" class="btn btn-info mr-2" @click="retour()"><i class="fa fa-arrow-left" aria-hidden="true"></i> Retour</button>
                <label class="border border-lg py-2 px-3 mb-0 mr-2 bg-white">Identifiant: <span class="text-danger text-uppercase">{{ current_proprio.identifiant }}</span></label> 
                <label class="border border-lg py-2 px-3 mb-0 mr-2 bg-white">Propriétaire: <span class="text-info text-uppercase">{{ current_proprio.nom }} {{ current_proprio.prenom }}</span></label> 
                <label class="border border-lg py-2 px-3 mb-0 mr-2 bg-white">Adresse: <span class="text-info text-uppercase">{{ current_proprio.adresse }}</span></label> 
            </div>
            <button type="button" class="btn btn-lg btn-primary" data-toggle="modal" data-target="#addNewRepre" v-on:click="newModalRep" ><i class="fa fa-plus-square"></i> Ajouter un représentant</button>
        </div>
             
        <div class="table-responsive">
            <table class="table table-hover table-striped">
              <thead class="bg-white">
                <tr>
                    <th>Identifiant</th>
                    <th>Nom & Prénom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Piéce</th>
                    <th class="text-right">Action</th>
                </tr>
                <tr>
                    <th colspan="9" class="position-relative p-0">
                        <div class="loader-line" :class="[isLoadingTabRepr?'d-block':'d-none']"></div>
                    </th>
                </tr>
              </thead>
              <tbody>
                <template v-if="!representants.data || !representants.data.length">
                        <tr><td colspan="10" class="bg-white text-center" v-if="checking">Aucun résultat!</td></tr>
                    </template>
                <tr v-for="repre in representants.data" :key="repre.identifiant">
                    <td class="align-middle"><h5 class="mb-0"><label class="badge badge-primary mb-0">{{repre.identifiant}}</label></h5></td>
                    <td class="align-middle">
                        {{repre.civilite}} {{repre.nom}} {{repre.prenom}}
                    </td>
                   
                    <td class="align-middle">
                        {{repre.email}}
                    </td>
                    <td class="align-middle">
                        {{repre.ind}} {{repre.tel}}
                    </td>
                    <td class="align-middle">
                        {{repre.type_piece}} {{repre.num_piece}}
                    </td>
                    <td class="text-right">
                        <div class="w-100 d-flex justify-content-end">
                            <button class="btn btn-info mx-1" data-toggle="modal" data-target="#addNewRepre"  v-on:click="edit(repre)"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-danger" @click="deleteLocal(repre)"><i class="fa fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
              </tbody>
            </table>
        </div>
         <div class="d-flex mt-4 justify-content-center">
            <pagination
                :data="representants"
                :limit=10
                @pagination-change-page="getRepresentant"
            ></pagination>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="addNewRepre" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="addNewRepre" aria-hidden="true">
                
                <div class="modal-dialog modal-xl position-relative" role="document">
                    <div class="loader-line" :class="[isLoadingRepr?'d-block':'d-none']"></div>
                    <div class="modal-content">
                    <div class="modal-header align-items-center">
                        <h5 class="modal-title text-uppercase" v-show="!editmodeRepresenant"><strong><span class="text-primary"><u>Nouveau</u></span> Représentant</strong></h5>
                        <h5 class="modal-title" v-show="editmodeRepresenant"><strong><span class="text-primary"><u>Editer</u></span> Local</strong></h5>
                         <label class="ml-3 border border-lg py-1 px-3 mb-0 mr-2 bg-white">Propriétaire: <span class="text-info text-uppercase">{{ current_proprio.nom }} {{ current_proprio.prenom }}</span></label> 
                          <label class="ml-3 border border-lg py-1 px-3 mb-0 mr-2 bg-white">Tel: <span class="text-info text-uppercase">{{ current_proprio.ind1 }} {{ current_proprio.tel1 }}</span></label> 
                        <button type="button" class="close" ref="closePopupLocal" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 

                    <form @submit.prevent="createRepr()">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 d-flex justify-content-between">
                                    <div class="form-group">
                                        <label>Civilité <span class="text-danger">*</span></label>
                                        <div class="d-flex align-items-center mt-2">
                                            <input id="Mlle" v-model="formRepr.civilite" value="Mlle" type="radio">
                                            <label class="pr-3 mb-0 pl-1" for="Mlle">M<sup>lle</sup></label> 
                                            <input id="Mme" v-model="formRepr.civilite" value="Mme" type="radio">
                                            <label class="pr-3 mb-0 pl-1" for="Mme">M<sup>me</sup></label> 
                                            <input id="M" v-model="formRepr.civilite" value="M." type="radio">
                                            <label class="pr-3 mb-0 pl-1" for="M">M.</label>  
                                        </div>
                                       
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nom</label>
                                        <input v-model="formRepr.nom" type="text"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                   <div class="form-group">
                                    <label>Prénom</label>
                                        <input v-model="formRepr.prenom" type="text"
                                            class="form-control">
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label>Téléphone Mobile</label>
                                        <div class="d-flex">
                                            <vue-country-dropdown
                                                @onSelect="onSelectIndicatif1" :onlyCountries="['SN']" :showNameInput="false" :enabledCountryCode="true">
                                            </vue-country-dropdown>
                                            <input v-model="formRepr.tel" type="text"
                                            class="form-control ml-2" :class="{ 'border-danger': isSubmitted && !$v.formRepr.tel.required }">
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input v-model="formRepr.email" type="email"
                                            class="form-control" :class="{ 'border-danger': isSubmitted && !$v.formRepr.email.required }">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label>Type piéce</label>
                                        <select v-model="formRepr.type_piece" class="form-control" :class="{ 'border-danger': isSubmitted && !$v.formRepr.type_piece.required }">
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
                                        <input v-model="formRepr.numero_piece" type="text"
                                            class="form-control">
                                       
                                    </div>
                                </div>
                            </div>
                            
                             
                              
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button v-show="editmodeRepresenant" type="submit" class="btn btn-success">Enregister</button>
                            <button v-show="editmodeRepresenant" type="button" class="btn btn-warning"  data-dismiss="modal" @click="resetFormRepre()">Annuler</button>
                            <button v-show="!editmodeRepresenant" type="submit" class="btn btn-success">Créer</button>
                            <button  v-show="!editmodeRepresenant" type="button" class="btn btn-info btn" @click="resetFormRepre()">Réinitialiser</button>
                            <button  v-show="!editmodeRepresenant" type="button" class="btn btn-secondary btn" @click="resetFormRepre()" data-dismiss="modal">Annuler</button>
                        </div>
                      </form>
                    </div>
                </div>
            </div>
       
        <!-- modal carousel -->
        <modalCarousel></modalCarousel>
    </div>
</template>
<script>
import DatePicker from 'vue2-datepicker';
import { EventBus } from '../../event-bus';
import modalCarousel from '../../components/modal/carousel.vue';
import VueCountryDropdown from 'vue-country-dropdown';
import { required, email, minLength, between } from 'vuelidate/lib/validators';
export default {
    props: [],
     components: {
       DatePicker, modalCarousel, VueCountryDropdown
      },
      data() { 
        return {
            checking: false,
            isLoadingRepr: false,
            current_proprio: {},
            representants:{},
            editmodeRepresenant: false,
            isLoadingTabRepr: false,
            formRepr: {
                civilite: '',
                proprio: '',
                id : '',
                nom: '',
                prenom: '',
                ind: '',
                tel: '',
                type_piece: '',
                numero_piece: '',
                email: ''
            },
            isSubmitted: false,
            paginate: 5
        }
      },
      validations: {
        formRepr : {
            tel: { required },
            civilite: { required },
            type_piece: { required }, 
            email:   { email, required }
        }
      },
      methods: {
       newModalRep(){
          this.editmodeRepresenant = false;
        },
        getRepresentant(page=1){

            this.isLoadingTabRepr = true;
            const data = new FormData();
            data.append('proprio_id', this.current_proprio.identifiant);
            axios.post("/representant/listing?paginate="+ this.paginate+'&page=' + page, data).then(responses => {
              console.log(responses);
               this.representants = responses.data;
               this.isLoadingTabRepr = false;
               this.checking = true;
            }).catch(errors => { 

            // react on errors.

            })
        },
        retour(){
            EventBus.$emit('BACK', { 
                action: false
            });
        },
        createRepr(){
            this.isSubmitted = true;
            // stop here if form is invalid
            this.$v.formRepr.$touch();

            if (this.$v.formRepr.$invalid) {
                return;
            }

            const data = new FormData();
            data.append('proprio_id', this.current_proprio.identifiant);
            data.append('nom', this.formRepr.nom);
            data.append('civilite', this.formRepr.civilite);
            data.append('prenom', this.formRepr.prenom);
            data.append('indicatif', this.formRepr.ind);
            data.append('telephone', this.formRepr.tel);
            data.append('email', this.formRepr.email);
            data.append('type_piece', this.formRepr.type_piece);
            data.append('num_piece', this.formRepr.numero_piece);

            let action = "create";

            if(this.editmodeRepresenant){
                action = "modify/"+this.formRepr.id;
            }

            this.isLoading = true;

     
            axios.post("/representant/"+action, data,  {
                headers: {
                    'Content-Type': 'multipart/form-data'
                } 
            }).then(response => {
              
                if(response.data.code==0){
                    this.isLoading = false;
                    this.$refs.closePopupLocal.click();
                    this.resetFormRepre();

                    Vue.swal.fire(
                      'succés!',
                      'Représentant ajouté avec succés!',
                      'success'
                    );

                    this.getRepresentant();

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
        resetFormRepre(){
            this.formRepr.nom = '';
            this.formRepr.prenom = '';
            this.formRepr.civilite= '';
            this.formRepr.ind = '';
            this.formRepr.tel = '';
            this.formRepr.type_piece = '';
            this.formRepr.numero_piece = '';
            this.formRepr.email = '';
          
        },
        edit(current){
            this.editmodeRepresenant = true;
            this.formRepr.id = current.identifiant;
            this.formRepr.civilite =  current.civilite;
            this.formRepr.nom = current.nom;
            this.formRepr.prenom = current.prenom;
            this.formRepr.ind = current.ind;
            this.formRepr.tel = current.tel;
            this.formRepr.type_piece = current.type_piece;
            this.formRepr.numero_piece = current.num_piece;
            this.formRepr.email = current.email;
        },

        onSelectIndicatif1({name, iso2, dialCode}) {
           this.formRepr.ind = dialCode; 
        },
       
        deleteLocal(item){
            Vue.swal.fire({
              title:"Suppression Représentant ",
              text: "Attention!!! cette opération est irréversible.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#3085d6',
              confirmButtonText: 'Oui, supprimer!'
            }).then((result) => {
            if (result.isConfirmed) {
    
                axios.delete('/representant/delete/'+item.identifiant).then(response => {
                    console.log(response);
                    if(response.data.code==0){
                         Vue.swal.fire(
                          'Suppression',
                          'Représentant supprimé avec succés',
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
        }
      },
         
      mounted() {
        
        EventBus.$on('VIEW_REPRESENTANT', (event) => {
            this.current_proprio = event.current;
            console.log(">>>>", this.current_proprio, "<<<<<<<");
            this.getRepresentant();
        });
      }
  }
</script> 
