<template>
    <div>
         <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex">
                <button type="button" class="btn btn-info mr-2" @click="retour()"><i class="fa fa-arrow-left" aria-hidden="true"></i> Retour</button>
                <label class="border border-lg py-2 px-3 mb-0 mr-2 bg-white">Identifiant: <span class="text-danger text-uppercase">{{ current_agence.identifiant }}</span></label>
                <label class="border border-lg py-2 px-3 mb-0 mr-2 bg-white">Agence: <span class="text-info text-uppercase">{{ current_agence.nom_agence }}</span></label>
                <label class="border border-lg py-2 px-3 mb-0 mr-2 bg-white">Adresse: <span class="text-info text-uppercase">{{ current_agence.adresse }}</span></label>
            </div>
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addNew" v-on:click="newModalRep" ><i class="fa fa-plus"></i> Ajouter une Filiale</button>
        </div>
             
        <div class="table-responsive">
            <table class="table table-hover table-striped">
              <thead class="bg-white">
                <tr>
                    <th>Identifiant</th>
                    <th>Nom filiale</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Logo</th>
                    <th class="text-right">Action</th>
                </tr>
                <tr>
                    <th colspan="9" class="position-relative p-0">
                        <div class="loader-line" :class="[isLoadingTabFiliale?'d-block':'d-none']"></div>
                    </th>
                </tr>
              </thead>
              <tbody>
                <template v-if="!filiales.data || !filiales.data.length">
                    <tr><td colspan="10" class="bg-white text-center" v-if="checking">Aucun résultat!</td></tr>
                </template>
                <tr v-for="filiale in filiales.data" :key="filiale.identifiant">
                    <td class="align-middle"><h5 class="mb-0"><label class="badge badge-primary mb-0">{{filiale.identifiant}}</label></h5></td>
                    <td class="align-middle">
                        {{filiale.nomFiliale}}
                    </td>
                   
                    <td class="align-middle">
                        {{filiale.email}}
                    </td>
                    <td class="align-middle">
                        {{filiale.ind1}} {{filiale.tel1}}
                    </td>
                    <td class="align-middle">
                       {{filiale.logo}}
                    </td>
                    <td class="text-right">
                        <div class="w-100 d-flex justify-content-end">
                            <button class="btn btn-info mx-1" data-toggle="modal" data-target="#addNew"  v-on:click="edit(filiale)"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-danger" @click="deleteFiliale(filiale)"><i class="fa fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
              </tbody>
            </table>
        </div>
         <div class="d-flex mt-4 justify-content-center">
            <pagination
                :data="filiales"
                :limit=10
                @pagination-change-page="getFiliale"
            ></pagination>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="addNew" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="addNew" aria-hidden="true">
                
                <div class="modal-dialog modal-xl position-relative" role="document">
                    <div class="loader-line" :class="[isLoadingRepr?'d-block':'d-none']"></div>
                    <div class="modal-content">
                    <div class="modal-header align-items-center">
                        <h5 class="modal-title text-uppercase" v-show="!editmodeFiliale"><strong><span class="text-primary"><u>Nouveau</u></span> Filiale</strong></h5>
                        <h5 class="modal-title" v-show="editmodeFiliale"><strong><span class="text-primary"><u>Editer</u></span> Filiale</strong></h5>
                         <label class="ml-3 border border-lg py-1 px-3 mb-0 mr-2 bg-white">Agence: <span class="text-info text-uppercase">{{ current_agence.nom_agence }}</span></label>
                          <label class="ml-3 border border-lg py-1 px-3 mb-0 mr-2 bg-white">Tel: <span class="text-info text-uppercase">{{ current_agence.ind1 }} {{ current_agence.tel1 }}</span></label>
                        <button type="button" class="close" ref="closePopupLocal" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 

                    <form @submit.prevent="createFiliale()">
                        <div class="modal-body">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nom Filiale<span class="required">*</span></label>
                                        <input v-model="formFiliale.nomFiliale" type="text"
                                            class="form-control"  :class="{ 'border-danger': isSubmitted && !$v.formFiliale.nomFiliale.required }">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                   <div class="form-group">
                                    <label>Adresse <span class="required">*</span></label>
                                        <input v-model="formFiliale.adresse" type="text"
                                            class="form-control"  :class="{ 'border-danger': isSubmitted && !$v.formFiliale.adresse.required }">
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <label>Téléphone Mobile <span class="required">*</span></label>
                                            <span  v-if="isSubmitted && !$v.formFiliale.tel.customTelValidation && $v.formFiliale.tel.required" class="error-message">{{ errorMessageTelephone() }}</span>
                                        </div>
                                        <div class="d-flex">
                                            <vue-country-dropdown
                                                @onSelect="onSelectIndicatif1" :onlyCountries="countriesAuthorizedISO2()" :showNameInput="false" :enabledCountryCode="true">
                                            </vue-country-dropdown>
                                            <input v-model="formFiliale.tel" type="text"
                                            class="form-control ml-2" :class="{ 'border-danger': isSubmitted && !$v.formFiliale.tel.required }">
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <label>Email <span class="required">*</span></label>
                                             <span  v-if="isSubmitted && !$v.formFiliale.email.emailValidation && $v.formFiliale.email.required" class="error-message">{{ errorMessageEmail() }}</span>
                                        </div>
                                        <input v-model="formFiliale.email" type="text"
                                            class="form-control" :class="{ 'border-danger': isSubmitted && !$v.formFiliale.email.required }">
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label>Ville <span class="required">*</span></label>
                                        <input v-model="formFiliale.ville" type="text"
                                            class="form-control" :class="{ 'border-danger': isSubmitted && !$v.formFiliale.ville.required }">

                                    </div>
                                </div>
                                <div class="col-md-6">
                                   <div class="form-group max-country">
                                        <label>Pays <span class="required">*</span></label>
                                        <input v-model="formFiliale.pays" type="text"
                                            class="form-control d-none"  :class="{ 'border-danger': isSubmitted && !$v.formFiliale.pays.required }">

                                            <vue-country-dropdown
                                                @onSelect="onSelect" :onlyCountries="['SN']" :showNameInput="true">
                                            </vue-country-dropdown>

                                    </div>
                                </div>
                            </div>

                            
                             
                              
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button v-show="editmodeFiliale" type="submit" class="btn btn-success">Enregister</button>
                            <button v-show="editmodeFiliale" type="button" class="btn btn-warning"  data-dismiss="modal" @click="resetFormFiliale()">Annuler</button>
                            <button v-show="!editmodeFiliale" type="submit" class="btn btn-success">Créer</button>
                            <button  v-show="!editmodeFiliale" type="button" class="btn btn-info btn" @click="resetFormFiliale()">Réinitialiser</button>
                            <button  v-show="!editmodeFiliale" type="button" class="btn btn-secondary btn" @click="resetFormFiliale()" data-dismiss="modal">Annuler</button>
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
            current_agence: {},
            filiales:{},
            editmodeFiliale: false,
            isLoadingTabFiliale: false,
            formFiliale: {
                nomFiliale: '',
                agence_id: '',
                id : '',
                nom: '',
                ind: '',
                tel: '',
                adresse: '',
                email: '',
                ville: '',
                pays: ''
            },
            isSubmitted: false,
            paginate: 5
        }
      },
      validations: {
        formFiliale : {
            nomFiliale: { required },
            adresse: { required },
            tel: { required,
                customTelValidation(value) {
                   const regex = this.getRegexForCountryTelMobile(this.formFiliale.ind);
                   if(!regex){
                    Vue.swal.fire(
                      'error!',
                      'Regex non défini',
                      'error'
                    )
                    return false
                   }
                    return regex.test(value);
                }
            },
            ville: { required },
            pays: { required },
            email:   { required,
                emailValidation(value) {
                    const regex = this.getRegexEmail();
                    console.log(">>>", regex.test(value))
                    return regex.test(value);
                }
            }

       }
      },
      methods: {
       newModalRep(){
          this.editmodeFiliale = false;
        },
        getFiliale(page=1){

            this.isLoadingTabFiliale = true;
            const data = new FormData();
            data.append('agence_id', this.current_agence.id);
            axios.post("/filiale/listing?paginate="+ this.paginate+'&page=' + page, data).then(responses => {
              console.log(responses);
               this.filiales = responses.data;
               this.isLoadingTabFiliale = false;
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
        createFiliale(){
            this.isSubmitted = true;
            // stop here if form is invalid
            this.$v.formFiliale.$touch();

            if (this.$v.formFiliale.$invalid) {
                return;
            }

            const data = new FormData();
            data.append('agence_id', this.current_agence.id);
            data.append('nom', this.formFiliale.nomFiliale);
            data.append('ind1', this.formFiliale.ind);
            data.append('tel1', this.formFiliale.tel);
            data.append('email', this.formFiliale.email);
            data.append('adresse', this.formFiliale.adresse);
            data.append('ville', this.formFiliale.ville);
            data.append('pays', this.formFiliale.pays);

            let action = "create";

            if(this.editmodeFiliale){
                action = "modify/"+this.formFiliale.id;
            }

            this.isLoading = true;

     
            axios.post("/filiale/"+action, data,  {
                headers: {
                    'Content-Type': 'multipart/form-data'
                } 
            }).then(response => {
              
                if(response.data.code==0){
                    this.isLoading = false;
                    this.$refs.closePopupLocal.click();
                    this.resetFormFiliale();

                    Vue.swal.fire(
                      'succés!',
                      'Filiale ajoutée avec succés!',
                      'success'
                    );

                    this.getFiliale();

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
           this.formFiliale.pays = iso2;
        },
        onSelectIndicatif1({name, iso2, dialCode}) {
           this.formFiliale.indicatif1 = dialCode;
        },
        onSelectIndicatif2({name, iso2, dialCode}) {
           this.formFiliale.indicatif2 = dialCode;
        },
        resetFormFiliale(){
            this.isSubmitted = false;
            this.formFiliale.nomFiliale = '';
            this.formFiliale.ind = '';
            this.formFiliale.tel = '';
            this.formFiliale.type_piece = '';
            this.formFiliale.numero_piece = '';
            this.formFiliale.email = '';
          
        },
        edit(current){
            this.editmodeFiliale = true;
            this.formFiliale.id = current.identifiant;
            this.formFiliale.nomFiliale = current.nom;
            this.formFiliale.prenom = current.prenom;
            this.formFiliale.ind = current.ind;
            this.formFiliale.tel = current.tel;
            this.formFiliale.email = current.email;
        },

        onSelectIndicatif1({name, iso2, dialCode}) {
           this.formFiliale.ind = dialCode;
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
    
                axios.delete('/filiale/delete/'+item.identifiant).then(response => {
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
        
        EventBus.$on('VIEW_FILIALE', (event) => {
            this.current_agence = event.current_agence;
            console.log(">>>>", this.current_agence, "<<<<<<<");
            this.getFiliale();
        });
      }
  }
</script> 
