<template>
    <div>
         <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex">
                <button type="button" class="btn btn-warning mr-2" @click="retour()"><i class="fa fa-arrow-left" aria-hidden="true"></i> Retour</button>
                <label class="border border-lg py-2 px-3 mb-0 mr-2 bg-white">Identifiant: <span class="text-danger text-uppercase">{{ current_bien.proprio }}</span></label> 
                <label class="border border-lg py-2 px-3 mb-0 mr-2 bg-white">Propriétaire: <span class="text-info text-uppercase">{{ current_bien.proprio_nom }} {{ current_bien.proprio_prenom }}</span></label> 
                <label class="border border-lg py-2 px-3 mb-0 mr-2 bg-white">Adresse: <span class="text-info text-uppercase">{{ current_bien.adresse }}</span></label> 
            </div>
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addNewLocal" v-on:click="newModalLocal" ><i class="fa fa-plus"></i> Ajouter un nouveau local</button>
        </div>
             
        <div class="table-responsive">
            <table class="table table-hover table-striped">
              <thead class="bg-white">
                <tr>
                    <th>Identifiant</th>
                    <th class="text-nowrap">Type Local</th>
                    <th class="text-nowrap" ttle="Type Local">Type de Local</th>
                    <th class="text-nowrap" title="Prix du Loyer Hors Charge">Prix Loyer</th>
                    <th>Détails</th>
                    <th>Photo</th>
                    <th class="text-right">Action</th>
                </tr>
                <tr>
                    <th colspan="9" class="position-relative p-0">
                        <div class="loader-line" :class="[isLoadingTabLocal?'d-block':'d-none']"></div>
                    </th>
                </tr>
              </thead>
              <tbody>
                <template v-if="!local.data || !local.data.length">
                        <tr><td colspan="10" class="bg-white text-center" v-if="checking">Aucun résultat!</td></tr>
                    </template>
                <tr v-for="lo in local.data" :key="lo.identifiant">
                    <td><h5 class="mb-0"><label class="badge badge-primary mb-0">{{lo.identifiant}}</label></h5></td>
                    <td>
                        <span class="text-success text-uppercase font-weight-bold">{{lo.type_local}}</span>
                    </td>
                    <td>
                        <span class="text-danger text-uppercase font-weight-bold">{{lo.type_location}}</span>
                    </td>
                    <td class="font-weight-bold text-dark">{{lo.prix_loyer}} FCFA</td>
                    <td clss="align-middle">
                        <ul class="ml-0 pl-0">
                            <li>Superficie: {{lo.superficie}}</li>
                            <li>Nombre de piéce: {{lo.nombre_piece}}</li>
                            <li>Salle de bain: {{lo.salle_bain}}</li>
                            <li>Description: {{lo.description}}</li>
                        </ul>
                    </td>
                    <td>
                        <span v-for="photo in lo.photo" class="mr-2 cursor-pointer">
                            <img :src="'/assets/biens/'+photo" height="38" data-toggle="modal" data-target="#carouselPhoto" @click="setKyc(lo)"/>
                        </span>
                        <h1 v-if="lo.photo.length==0"><i class="fa fa-camera-retro" aria-hidden="true"></i></h1>
                    </td>
                    <td class="text-right">
                        <div class="w-100 d-flex justify-content-end">
                            <button class="btn btn-primary" @click="view(lo)" data-toggle="modal" data-target="#moreInfo" v-on:click="moreInfo"><i class="fa fa-eye"></i></button>
                            <button class="btn btn-info mx-1" data-toggle="modal" data-target="#addNewLocal"  v-on:click="edit(lo)"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-danger" @click="deleteLocal(lo)"><i class="fa fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
              </tbody>
            </table>
        </div>
         <div class="d-flex mt-4 justify-content-center">
            <pagination
                :data="local"
                :limit=10
                @pagination-change-page="getLocal"
            ></pagination>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="addNewLocal" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="addNewLocal" aria-hidden="true">
                
                <div class="modal-dialog modal-xl position-relative" role="document">
                    <div class="loader-line" :class="[isLoadingLocal?'d-block':'d-none']"></div>
                    <div class="modal-content">
                    <div class="modal-header align-items-center">
                        <h5 class="modal-title text-uppercase" v-show="!editmodeLocal"><strong><span class="text-primary"><u>Nouveau</u></span> Local</strong></h5>
                        <h5 class="modal-title" v-show="editmodeLocal"><strong><span class="text-primary"><u>Editer</u></span> Local</strong></h5>
                         <label class="ml-3 border border-lg py-1 px-3 mb-0 mr-2 bg-white">Propriétaire: <span class="text-info text-uppercase">{{ current_bien.proprio_nom }} {{ current_bien.proprio_prenom }}</span></label> 
                          <label class="ml-3 border border-lg py-1 px-3 mb-0 mr-2 bg-white">Tel: <span class="text-info text-uppercase">{{ current_bien.proprio_ind }} {{ current_bien.proprio_tel }}</span></label> 
                        <button type="button" class="close" ref="closePopupLocal" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 

                    <form @submit.prevent="createLocal()">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Type Local</label>
                                        <select class="form-control" v-model="formLocal.type_local" :class="{ 'border-danger': isSubmitted && !$v.formLocal.type_local.required }">
                                            <option value="">Choisir</option>
                                            <option value="maison">Maison</option>
                                            <option value="appartement">Appartement</option>
                                            <option value="studio">Studio</option>
                                            <option value="chambre">Chambre</option>
                                            <option value="magasin">Magasin</option>
                                            <option value="bureau">Bureau</option>
                                            <option value="depôt">Depôt</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Type de Location proposé</label>
                                        <select class="form-control" v-model="formLocal.type_location" :class="{ 'border-danger': isSubmitted && !$v.formLocal.type_location.required }">
                                            <option value="">Choisir</option>
                                            <option value="meublée">Meublée</option>
                                            <option value="vide">Vide</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Prix du Loyer hors charge</label>
                                        <input v-model="formLocal.prix_loyer" type="text"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                   <div class="form-group">
                                    <label>Montant des charges</label>
                                        <input v-model="formLocal.montant_charge" type="text"
                                            class="form-control">
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label>Superficie en m<sup>2</sup></label>
                                        <input v-model="formLocal.superficie" type="text"
                                            class="form-control">
                                       
                                    </div>
                                </div>
                                <div class="col-md-6">
                                   <div class="form-group max-country">
                                        <label>Nombre de piéces</label>
                                        <input v-model="formLocal.nombre_piece" type="number"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                           
                            <div class="row">
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label>Salle de Bain</label>
                                        <div class="d-flex align-items-center">
                                            <input id="oui" v-model="formLocal.salle_bain" value="oui" type="radio">
                                            <label class="pr-3 mb-0 pl-1 text-success" for="oui">Oui</label> 
                                            <input id="non" v-model="formLocal.salle_bain" value="non" type="radio">
                                            <label class="mb-0 pl-1 text-danger" for="non">Non</label>  
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label>Année de construction</label>
                                          <date-picker v-model="formLocal.annee_construction" class="w-100"  required valueType="YYYY-MM-DD" input-class="form-control w-100" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>
                                       
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-6">
                                   <div class="form-group d-flex justify-content-between flex-column">
                                        <label>Description</label>
                                        <textarea class="form-control" v-model="formLocal.description"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label>Photos</label>
                                        <input namela liga="file" multiple type="file" ref="attachmentsPhotosLocal" 
                                            class="form-control border-0 pl-0" v-on:change="handleFileUploadLocal()">
                                    </div>
                                    <div v-if="editmodeLocal">
                                         <span v-for="photo in editKyc" class="mr-3 cursor-pointer" v-on:click="supprimerPhoto(photo, editKyc, formLocal.id)">
                                            <img :src="'/assets/biens/'+photo" height="50" data-toggle="modal" data-target="#carouselPhoto" @click="setKyc(pro)"/>
                                            <i class="text-danger fa fa-times"></i>
                                        </span>
                                       
                                    </div>
                                </div>
                            </div>
                              
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button v-show="editmodeLocal" type="submit" class="btn btn-success">Enregister</button>
                            <button v-show="editmodeLocal" type="button" class="btn btn-warning"  data-dismiss="modal" @click="resetFormLocal()">Annuler</button>
                            <button v-show="!editmodeLocal" type="submit" class="btn btn-success">Créer</button>
                            <button  v-show="!editmodeLocal" type="button" class="btn btn-info btn" @click="resetFormLocal()">Réinitialiser</button>
                            <button  v-show="!editmodeLocal" type="button" class="btn btn-secondary btn" @click="resetFormLocal()" data-dismiss="modal">Annuler</button>
                        </div>
                      </form>
                    </div>
                </div>
            </div>
         <!-- modal info Local-->
        <InfoLocal></InfoLocal>
        <!-- modal carousel -->
        <modalCarousel></modalCarousel>
    </div>
</template>
<script>
import DatePicker from 'vue2-datepicker';
import { EventBus } from '../../event-bus';
import modalCarousel from '../../components/modal/carousel.vue';
import { required, email, minLength, between } from 'vuelidate/lib/validators';
import InfoLocal from './InfoLocal.vue';
export default {
    props: [],
     components: {
       DatePicker, modalCarousel, InfoLocal
      },
      data() { 
        return {
            checking: false,
            isLoadingLocal: false,
            current_bien: {},
            local:{},
            editmodeLocal: false,
            isLoadingTabLocal: false,
            formLocal: {
                proprio: '',
                id : '',
                montant_charge: '',
                nombre_piece: '',
                salle_bain: '',
                superficie:'',
                description: '',
                annee_construction: '',
                type_local:'',
                type_location:'',
                photo_local: null
            },
            attachmentsPhotosLocal: [],
            isSubmitted: false,
            paginate: 5,
            editKyc: []
        }
      },
      validations: {
        formLocal : {
            type_local: { required },
            type_location: { required }
        }
      },
      methods: {
       newModalLocal(){
          this.editmodeLocal = false;
        },
        getLocal(page=1){

            this.isLoadingTabLocal = true;
            const data = new FormData();
            data.append('bien_id', this.current_bien.identifiant);
            axios.post("/local/listing?paginate="+ this.paginate+'&page=' + page, data).then(responses => {
              console.log(responses);
               this.local = responses.data;
               this.isLoadingTabLocal = false;
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
        createLocal(){
            this.isSubmitted = true;
            // stop here if form is invalid
            this.$v.formLocal.$touch();

            if (this.$v.formLocal.$invalid) {
                return;
            }

            const data = new FormData();
            data.append('bien_id', this.current_bien.identifiant);
            data.append('type_local', this.formLocal.type_local);
            data.append('type_location', this.formLocal.type_location);
            data.append('montant_charge', this.formLocal.montant_charge);
            data.append('prix_loyer', this.formLocal.prix_loyer);
            data.append('superficie', this.formLocal.superficie);
            data.append('nombre_piece', this.formLocal.nombre_piece);
            data.append('salle_bain', this.formLocal.salle_bain);
            data.append('description', this.formLocal.description);
            data.append('annee_construction', this.formLocal.annee_construction);
            data.append('file[]', this.attachmentsPhotosLocal);

            for (let i = 0; i < this.attachmentsPhotosLocal.length; i++) {
                data.append('files' + i, this.attachmentsPhotosLocal[i]);
            }
            data.append('TotalFiles', this.attachmentsPhotosLocal.length);

            let action = "create";

            if(this.editmodeLocal){
                data.append('additionalFile', JSON.stringify(this.editKyc));
                action = "modify/"+this.formLocal.id;
            }

            this.isLoading = true;

     
            axios.post("/local/"+action, data,  {
                headers: {
                    'Content-Type': 'multipart/form-data'
                } 
            }).then(response => {
              
                if(response.data.code==0){
                    this.isLoading = false;
                    this.$refs.closePopupLocal.click();
                    this.resetFormLocal();

                    Vue.swal.fire(
                      'succés!',
                      'Local crée avec succés!',
                      'success'
                    );

                    this.getLocal();

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
        resetFormLocal(){
            this.formLocal.montant_charge = '';
            this.formLocal.nombre_piece = '';
            this.formLocal.salle_bain = '';
            this.formLocal.superficie = '';
            this.formLocal.prix_loyer = '';
            this.formLocal.description = '';
            this.formLocal.annee_construction = '';
            this.formLocal.type_local = '';
            this.formLocal.type_location = '';
            this.formLocal.photo_local = null;
        },
        moreInfo(){

        },
        handleFileUploadLocal(){
            this.attachmentsPhotosLocal = [];
            for(var i=0; i<this.$refs.attachmentsPhotosLocal.files.length;i++){
                this.attachmentsPhotosLocal.push(this.$refs.attachmentsPhotosLocal.files[i])
            }
        },
        setKyc(prop){
             // get photo
            EventBus.$emit('VIEW_CAROUSEL', { 
                kyc: prop.photo,
                path: '/assets/biens/'
            });
        },
        view(local){
            EventBus.$emit('VIEW_INFO_LOCAL', { 
                info:  local
            });
        },
        edit(current){
            this.editmodeLocal = true;
            this.formLocal.id = current.identifiant;
            this.formLocal.montant_charge = current.montant_charge;
            this.formLocal.nombre_piece = current.nombre_piece;
            this.formLocal.salle_bain = current.salle_bain;
            this.formLocal.superficie = current.superficie;
            this.formLocal.prix_loyer = current.prix_loyer;
            this.formLocal.description = current.description;
            this.formLocal.annee_construction = current.annee_cons_natif;
            this.formLocal.type_local = current.type_local;
            this.formLocal.type_location = current.type_location; 
            this.editKyc = current.photo;
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
                axios.post('/local/remphoto', data).then(response => {
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
        deleteLocal(item){
            Vue.swal.fire({
              title:"Suppression Local ",
              text: "Attention!!! cette opération est irréversible.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#3085d6',
              confirmButtonText: 'Oui, supprimer!'
            }).then((result) => {
            if (result.isConfirmed) {
    
                axios.delete('/local/delete/'+item.identifiant).then(response => {
                    console.log(response);
                    if(response.data.code==0){
                         Vue.swal.fire(
                          'Suppression',
                          'Local supprimé avec succés',
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
        
        EventBus.$on('VIEW_LOCAL', (event) => {
            this.current_bien = event.current;
            this.getLocal();
        });
      }
  }
</script> 
