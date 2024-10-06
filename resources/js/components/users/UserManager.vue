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
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addNew" v-on:click="newModal" ><i class="fa fa-plus"></i> Créer un utilisateur</button>
    </div>

    <!-- Liste des utilisateurs -->
    <div>
      <h2>Liste des utilisateurs</h2>
      <div class="table-responsive">
          <table class="table table-striped">
            <thead class="">
              <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Nom d'utilisateur</th>
                <th>Email</th>
                <th>Rôle</th>
                <th></th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr v-for="user in users" :key="user.id">
                <td>{{ user.id }}</td>
                <td>{{ user.name }}</td>
                <td>{{ user.username }}</td>
                <td>{{ user.email }}</td>
                <td><label class="badge badge-primary">{{ user.roles.map(role => role.name).join(', ') }}</label></td>
                <td class="text-right align-middle">
                  <div class="d-inline-flex">
                    <!--button @click="editUser(user.id)" class="btn btn-sm btn-info border-2 ml-1">
                      <i class="fa fa-edit"></i>
                    </button-->
                    <button @click="deleteUser(user.id)" class="btn btn-sm btn-danger border-2 ml-1">
                      <i class="fa fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
    </div>

        <!-- Formulaire de création d'utilisateur -->
         <div class="modal fade" id="addNew" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="addNew" aria-hidden="true">

                <div class="modal-dialog modal-xl position-relative" role="document">
                    <div class="loader-line" :class="[isLoading?'d-block':'d-none']"></div>
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-uppercase" v-show="!editmode"><strong><span class="text-primary"><u>Créer</u></span>  un utilisateur</strong></h5>
                        <h5 class="modal-title" v-show="editmode"><strong><span class="text-primary"><u>Editer</u></span> Propriétaire</strong></h5>
                        <button type="button" class="close" ref="closePopup" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form @submit.prevent="createUser()">
                        <div class="modal-body">
                            <div class="d-flex">
                               <!-- Champs personnel -->
                               <div class="orientation mr-3 text-left border-right pr-2">
                                    <label class="text-uppercase text-info titleform border-info pt-2">Infos Perso</label>
                                </div>
                                <div>
                                   <div class="row">
                                    <div class="col-md-6 d-flex justify-content-between">
                                        <div class="form-group w-49">
                                            <label for="pers_nom">Nom <span class="required">*</span></label>
                                            <input v-model="newUser.pers_nom" type="text"
                                                class="form-control" id="pers_nom"  :class="{ 'border-danger': isSubmitted && (!$v.newUser.pers_nom.required)}">
                                        </div>
                                         <div class="form-group w-49">
                                            <label for="pers_prenom">Prénom <span class="required">*</span></label>
                                            <input v-model="newUser.pers_prenom" type="text"
                                                class="form-control" id="pers_prenom"  :class="{ 'border-danger': isSubmitted && (!$v.newUser.pers_prenom.required)}"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-between">
                                        <div class="form-group w-49">
                                            <div class="d-flex justify-content-between align-items-baseline">
                                                <label>Téléphone Mobile <span class="required">*</span></label>
                                                <span  v-if="isSubmitted && !$v.newUser.pers_tel_1.customTelValidation && $v.newUser.pers_tel_1.required" class="error-message">{{ errorMessageTelephone() }}</span>
                                            </div>
                                            <div class="d-flex">
                                                <vue-country-dropdown
                                                    @onSelect="onSelectIndicatif1" :onlyCountries="countriesAuthorizedISO2()" :showNameInput="false" :enabledCountryCode="true">
                                                </vue-country-dropdown>
                                                <input v-model="newUser.pers_tel_1" type="text"
                                                class="form-control ml-2" :class="{ 'border-danger': isSubmitted && (!$v.newUser.pers_tel_1.required || !$v.newUser.pers_tel_1.customTelValidation)}">
                                            </div>

                                        </div>
                                        <div class="form-group w-49">

                                            <div class="d-flex justify-content-between align-items-baseline">
                                                <label>Téléphone Fixe</label>
                                                <template v-if="newUser.pers_tel_2!=''">
                                                    <span  v-if="isSubmitted && !$v.newUser.pers_tel_2.customTelFixeValidation" class="error-message">{{ errorMessageTelephone() }}</span>
                                                </template>

                                            </div>
                                            <div class="d-flex">
                                                <vue-country-dropdown
                                                        @onSelect="onSelectIndicatif2" :onlyCountries="countriesAuthorizedISO2()" :showNameInput="false" :enabledCountryCode="true">
                                                    </vue-country-dropdown>
                                                <input v-model="newUser.pers_tel_2" type="text"
                                                    class="form-control ml-2"  :class="{ 'border-danger': isSubmitted && !$v.newUser.pers_tel_2.customTelFixeValidation}">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <label for="pers_adress">Adresse</label>
                                          <input type="text" class="form-control" id="pers_adress" v-model="newUser.pers_adress">
                                      </div>
                                  </div>
                                   <div class="col-md-6 d-flex justify-content-between">
                                         <div class="form-group w-49">
                                           <label for="pers_ville">Ville</label>
                                            <input type="text" class="form-control" id="pers_ville" v-model="newUser.pers_ville">

                                        </div>
                                        <div class="form-group max-country w-49">
                                            <label for="pers_pays">Pays</label>
                                            <vue-country-dropdown
                                                    @onSelect="onSelectPays" :onlyCountries="allCountriesISO2()" :showNameInput="true">
                                            </vue-country-dropdown>
                                        </div>
                                  </div>
                                </div>

                                </div>
                            </div>



                          <div class="d-flex">
                            <div class="orientation mr-3 text-left border-right pr-2">
                                <label class="text-uppercase text-info titleform border-info pt-2">Création compte</label>
                            </div>
                            <div style="width: 100%;" class="py-3">
                              <div class="row">
                                <div class="col-md-6 d-flex justify-content-between">
                                    <div class="form-group w-49">
                                     <div class="form-group">
                                        <label for="nomUtilisateur">Nom d'utilisateur <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="nomUtilisateur" v-model="newUser.nomUtilisateur" :class="{ 'border-danger': isSubmitted && (!$v.newUser.nomUtilisateur.required)}">
                                      </div>
                                    </div>
                                    <div class="form-group w-49">
                                     <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" v-model="newUser.email">
                                      </div>
                                    </div>
                                </div>
                                 <div class="col-md-6">
                                      <div class="form-group">
                                        <label for="role">Rôle <span class="required">*</span></label>
                                        <select class="form-control" id="role" v-model="newUser.role"  :class="{ 'border-danger': isSubmitted && (!$v.newUser.role.required)}">
                                          <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
                                        </select>
                                      </div>
                                </div>
                              </div>

                              <div class="row">
                                <div class="col-md-6 d-flex justify-content-between">
                                    <div class="form-group w-49">
                                        <label for="password">Mot de passe<span class="required">*</span></label>
                                        <input type="password" class="form-control" id="password" v-model="newUser.password">
                                    </div>
                                    <div class="form-group w-49">
                                        <label for="rpassword">Confirmer Mot de passe <span class="required">*</span></label>
                                        <input type="password" class="form-control" id="rpassword" v-model="newUser.rpassword">
                                    </div>
                                </div>
                                <div class="col-md-6 d-flex justify-content-between">
                                    <div class="form-group w-49">
                                        <label for="role">Agence <span class="required">*</span></label>

                                         <select class="form-control" v-model="newUser.selectedAgenceId" @change="filterFiliales" :class="{ 'border-danger': isSubmitted && (!$v.newUser.agence.required)}">
                                          <option value="" disabled>Sélectionner une agence</option>
                                          <option v-for="agence in agences" :key="agence.id" :value="agence.id">
                                            {{ agence.agence_nom }}
                                          </option>
                                        </select>
                                    </div>
                                     <div class="form-group w-49">
                                        <label for="filiale">Filiales</label>
                                         <select class="form-control" v-model="newUser.selectedFilialeId">
                                          <option value="" disabled>Sélectionner une filiale</option>
                                          <option v-for="filiale in newUser.filteredFiliales" :key="filiale.id" :value="filiale.id">
                                            {{ filiale.filiale_name }}
                                          </option>
                                        </select>
                                    </div>
                                </div>
                              </div>
                            </div>
                        </div>


                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="submit" class="btn btn-success" :disabled="isLoading ? true: false">Créer</button>
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
import { required, email, minLength, between } from 'vuelidate/lib/validators';

export default {
  components: { DatePicker, VueCountryDropdown },
   props: {
        agences: {type: Array, default: () => [] },
        filiales:  {type: Array, default: () => [] }
     },
  data() {
    return {
      isSubmitted: false,
      isLoading: false,
      editmode: false,
      paginate: 5,
      users: [],
      roles: [],
      newUser: {
        name: '',
        nomUtilisateur: '',
        email: '',
        password: '',
        rpassword: '',
        role: '',
        pers_nom: '',
        pers_prenom: '',
        pers_email: '',
        pers_ind_1: '',
        pers_tel_1: '',
        pers_ind_2: '',
        pers_tel_2: '',
        pers_adress: '',
        pers_ville: '',
        pers_pays: '',
        selectedAgenceId: null,
        selectedFilialeId: null,
        filteredFiliales: []
      },

    };
  },
  created() {
    this.fetchUsers();
    this.fetchRoles();
    console.log("agence >>", this.agences)
    console.log("filiales >>", this.filiales)
  },
  validations: {
      newUser : {
          nomUtilisateur: { required },
          pers_nom: { required },
          pers_prenom: { required },
          role: { required },
          agence: { required },
          pers_tel_1: {
              required,
              customTelValidation(value) {
                console.log("value regex >>>", this.newUser.pers_ind_1)
                 const regex = this.getRegexForCountryTelMobile(this.newUser.pers_ind_1);
                 if(!regex){
                  Vue.swal.fire(
                    'error!',
                    'Regex non défini',
                    'error'
                  )
                  return false
                 }
                  return regex.test(value);
              },
          },
          pers_tel_2: {
                customTelFixeValidation(value) {
                    if(value=='') return true;
                   const regex = this.getRegexForCountryTelFixe(this.newUser.pers_ind_2);
                   if(!regex){
                    Vue.swal.fire(
                      'error!',
                      'Regex non défini 2',
                      'error'
                    )
                    return false
                   }
                    return regex.test(value);
                }
            },

      }
    },
  methods: {
    onSelectIndicatif1({name, iso2, dialCode}) {
       this.newUser.pers_ind_1 = dialCode;
    },
    onSelectIndicatif2({name, iso2, dialCode}) {
       this.newUser.pers_ind_2 = dialCode;
    },
    onSelectPays({name, iso2, dialCode}) {
       this.newUser.pers_pays = iso2;
    },
    newModal(){
      this.editmode = false;
    },
    fetchUsers() {
      axios.get('/api/users')
        .then(response => {
          this.users = response.data;
        })
        .catch(error => {
          console.error('Erreur lors de la récupération des utilisateurs', error);
        });
    },
    fetchRoles() {
      axios.get('/api/roles')
        .then(response => {
          this.roles = response.data;
        })
        .catch(error => {
          console.error('Erreur lors de la récupération des rôles', error);
        });
    },
    createUser() {
      this.isSubmitted = true;

      // stop here if form is invalid
      this.$v.newUser.$touch();

      if (this.$v.newUser.$invalid) {
          return;
      }

      axios.post('/api/users', this.newUser)
        .then(response => {
            this.isSubmitted = false;
            Vue.swal.fire(
                'succés!',
                'Utilisateur créé avec succès',
                'success'
              );
          //this.users.push(response.data);
          this.resetForm();
          this.$refs.closePopup.click();

          this.fetchUsers();
        })
        .catch(error => {
          console.error('Erreur lors de la création de l\'utilisateur', error);
        });
    },
    resetForm(){
        this.newUser.name = '';
        this.newUser.name= '';
        this.newUser.nomUtilisateur= '';
        this.newUser.email= '';
        this.newUser.password= '';
        this.newUser.rpassword= '';
        this.newUser.role= '';
        this.newUser.pers_nom= '';
        this.newUser.pers_prenom= '';
        this.newUser.pers_email= '';
        this.newUser.pers_ind_1= '';
        this.newUser.pers_tel_1= '';
        this.newUser.pers_ind_2= '';
        this.newUser.pers_tel_2= '';
        this.newUser.pers_adresse= '';
        this.newUser.pers_ville= '';
        this.newUser.pers_pays= '';
    },
    editUser(userId) {
      // Implémenter la fonctionnalité de modification si nécessaire
    },
    deleteUser(userId) {
      axios.delete(`/api/users/${userId}`)
        .then(response => {
          this.users = this.users.filter(user => user.id !== userId);
          Vue.swal.fire(
            'succés!',
            'Utilisateur supprimé avec succès',
            'success'
          );
          this.fetchUsers();
        })
        .catch(error => {
          console.error('Erreur lors de la suppression de l\'utilisateur', error);
        });
    },
    resetForm() {
      this.newUser = {
        name: '',
        email: '',
        password: '',
        role: '',
        pers_nom: '',
        pers_prenom: '',
        pers_email: '',
        pers_ind_1: '',
        pers_tel_1: '',
        pers_ind_2: '',
        pers_tel_2: '',
        pers_adress: '',
        pers_ville: '',
        pers_pays: ''
      };
    },
    filterFiliales() {
      // Filtrer les filiales basées sur l'agence sélectionnée
      this.newUser.filteredFiliales = this.filiales.filter(filiale => filiale.agence_id === this.newUser.selectedAgenceId);
    }
  }
};
</script>

<style scoped>
/* Ajoutez des styles spécifiques à ce composant si nécessaire */
</style>
