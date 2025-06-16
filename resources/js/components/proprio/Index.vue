<template>
    <div>
        <template v-if="!viewRepresentant">
            <div class="topTable d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex justify-content-between align-items-center gap-15">
                    <div class="d-flex align-items-center">
                        <label class="text-nowrap mr-2 mb-0">Nbre de ligne par Page</label>
                        <select class="form-control form-control-sm" v-model="paginate">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                        </select>
                    </div>
                    <!-- Filtres -->

                    <div class="d-flex align-items-end gap-15">
                        <div style="width: 300px">
                            <v-select v-model="proprioSelected" @input="onInputSelectProprio" placeholder="Filtrer par propriétaire" :options="listProprio"  @option:selected="onProprioChoisi" label="item_data"></v-select>
                        </div>
                    </div>
                </div>

                <button v-if="hasPermission('Proprietaire.Ajouter')" type="button" class="btn btn-primary" data-toggle="modal" data-target="#addNew" v-on:click="newModal" ><i class="fa fa-plus"></i> Nouveau Propriétaire</button>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                  <thead class="bg-white">
                    <tr>
                        <th>Identifiant</th>
                        <th>Propriétaire</th>
                        <th>Téléphone</th>
                        <th class="text-center">Bien loué</th>
                        <th>Locataire Actif</th>
                        <th class="text-right">Revenus Locatif</th>
                        <!--th>Photo piéce</th-->
                        <th class="text-right">Action</th>
                    </tr>
                    <tr>
                        <th colspan="9" class="position-relative p-0">
                            <div class="loader-line" :class="[isLoadingTab?'d-block':'d-none']"></div>
                        </th>
                    </tr>
                  </thead>
                  <tbody>
                    <template v-if="!hasData">
                        <tr><td colspan="7" class="bg-white text-center">{{isLoadingTab?'Chargement en cours...':'Aucune donnée!'}}</td></tr>
                    </template>
                    <tr v-for="pro in proprios.data" :key="pro.identifiant">
                        <td class="align-middle">
                            <h5 class="mb-0">
                                <label class="badge badge-primary mb-0">{{pro.identifiant}}</label>
                            </h5>
                        </td>
                        <td class="align-middle">{{pro.nom}} {{pro.prenom}}</td>

                        <td class="align-middle"> 
                            <div>{{pro.ind1}} {{pro.tel1}}</div>
                        </td>
                        <td class="align-middle text-center">


                            <button title="Voir la liste" :disabled="pro.nbreBienLoue > 0
                             ?false:true" class="btn btn-sm border-2 ml-1" :class="'btn-'+(pro.nbreBienLoue > 0?'primary':'secondary')" @click="view(pro)" data-toggle="modal" data-target="#modalBienLoue">{{pro.nbreBienLoue}}</button>
                        </td>
                        <td class="align-middle">
                             <button title="Voir la liste" :disabled="pro.nbreLocatairesActifs > 0
                             ?false:true" class="btn btn-sm border-2 ml-1" :class="'btn-'+(pro.nbreLocatairesActifs > 0?'success':'danger')" @click="view(pro)" data-toggle="modal" data-target="#modalLocataireActif">{{ pro.nbreLocatairesActifs }}</button></td>
                        <td class="align-middle text-right">
                            {{ helper_separator_amount(pro.revenus_locatifs) }}
                        </td>
                        <!--td class="align-middle">
                            <span v-for="photo in pro.kyc" class="mr-2 cursor-pointer">
                                <img :src="'/assets/kyc/'+photo" height="30" data-toggle="modal" data-target="#carouselPhoto" @click="setKyc(pro)"/>
                            </span>
                        </td-->
                        <td class="text-right align-middle">
                            <div class="d-inline-flex">
                                <button class="btn  btn-sm  btn-success mr-2" data-toggle="modal" data-target="#badgeProprietaire" @click="showBadge(pro)"><i class="fa fa-id-badge" aria-hidden="true"></i> Badge</button>
                                <button type="button" title="Représentants" class="btn btn-sm border-2 border-info font-weight-bold position-relative mr-2 hover-info" @click="setUpRepresentant(pro)"><i class="fa fa-users"></i> <span class="badge badge-info badge-light position-absolute total-right-corner">{{ pro.nbreRespre }}</span>
                                </button>
                                <button title="Plus de détails" class="btn btn-sm border-2 btn-primary ml-1" @click="view(pro)" data-toggle="modal" data-target="#moreInfo" v-on:click="newModal"><i class="fa fa-eye"></i></button>
                                <button v-if="hasPermission('Proprietaire.Modifier')" title="Modifier" class="btn btn-sm btn-info border-2 ml-1" data-toggle="modal" data-target="#addNew"  v-on:click="edit(pro)"><i class="fa fa-edit"></i></button>
                                <button :disabled="pro.nbreLocatairesActifs>0?true:false" v-if="hasPermission('Proprietaire.Supprimer')" title="Supprimer" class="btn btn-sm btn-danger border-2 ml-1" @click="deleteProprio(pro)"><i class="fa fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                  </tbody>
                </table>
            </div>
             <div class="d-flex mt-4 justify-content-center">
                <pagination
                    :data="proprios"
                    :limit=10
                    @pagination-change-page="getProprio"
                ></pagination>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="addNew" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="addNew" aria-hidden="true">
                
                <div class="modal-dialog  modal-xl position-relative" role="document">
                    <div class="loader-line" :class="[isLoading?'d-block':'d-none']"></div>
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-uppercase" v-show="!editmode"><strong><span class="text-primary"><u>Nouveau</u></span> Propriétaire</strong></h5>
                        <h5 class="modal-title" v-show="editmode"><strong><span class="text-primary"><u>Editer</u></span> Propriétaire</strong></h5>
                        <button type="button" class="close" ref="closePopup" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 

                    <form @submit.prevent="createProprio()">
                        <div class="modal-body">
                            <div class="d-flex">
                                <div class="orientation mr-3 text-left border-right pr-2">
                                    <label class="text-uppercase text-info titleform border-info pt-2">Infos Perso</label>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="row">
                                        <div class="col-md-6 d-flex justify-content-between">
                                            <div class="form-group w-49">
                                                <label>Nom <span class="required">*</span></label>
                                                <input v-model="form.nom" type="text"
                                                    class="form-control" :class="{ 'border-danger': isSubmitted && !$v.form.nom.required }">
                                            </div>
                                             <div class="form-group w-49">
                                                <label>Prénom <span class="required">*</span></label>
                                                <input v-model="form.prenom" type="text"
                                                    class="form-control" :class="{ 'border-danger': isSubmitted && !$v.form.prenom.required }">
                                               
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-flex justify-content-between">
                                           <div class="form-group w-49">
                                                <label>Nationalié <span class="required">*</span></label>
                                                <select class="form-control" v-model="form.nationalite" :class="{ 'border-danger': isSubmitted && !$v.form.nationalite.required }">
                                                  <option value="" disabled>Sélectionner une nationalité</option>
                                                  <option v-for="nationalite in getNationale()" :key="nationalite" :value="nationalite">
                                                    {{ nationalite }}
                                                  </option>
                                                </select>
                                            </div>
                                            <div class="form-group max-country  w-49">
                                                <label>Profession <span class="required">*</span></label>
                                                 <input v-model="form.profession" type="text"
                                                    class="form-control" :class="{ 'border-danger': isSubmitted && !$v.form.profession.required }">
                                               
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 d-flex justify-content-between">
                                            <div class="form-group w-49">
                                                <label>Date de naissance</label>
                                                  <date-picker v-model="form.date_naissance" class="w-100"  required valueType="YYYY-MM-DD" input-class="form-control w-100" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>
                                            </div>
                                             <div class="form-group max-country  w-49">
                                                <label>Ville de naissance <span class="required">*</span></label>
                                                <input v-model="form.ville_naissance" type="text"
                                                    class="form-control" :class="{ 'border-danger': isSubmitted && !$v.form.ville_naissance.required }">
                                               
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-flex justify-content-between">
                                           <div class="form-group max-country  w-49">
                                                <label>Pays de naissance</label>
                                                <vue-country-dropdown
                                                        @onSelect="onSelectNaissance" :onlyCountries="allCountriesISO2()" :showNameInput="true">
                                                </vue-country-dropdown>
                                               
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 d-flex justify-content-between">
                                            <div class="form-group w-49">
                                                <label>Entreprise</label>
                                                <input v-model="form.entreprise" type="text"
                                                    class="form-control">
                                            </div>
                                             <div class="form-group w-49">
                                                <label>Compte bancaire</label>
                                                <input v-model="form.compte_bancaire" type="text" 
                                                    class="form-control">
                                               
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-flex justify-content-between">
                                           <div class="form-group">
                                                <label>Signature electronique</label>
                                                <input name="file" multiple type="file" ref="attachmentsSignature" 
                                                    class="form-control border-0 pl-0" v-on:change="handleFileUploadSign()">
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                           
                            <div class="d-flex"> 
                                <div class="orientation mr-3 text-left border-right pr-2">
                                    <label class="text-uppercase text-info titleform border-info pt-2">Infos de Contact</label>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">

                                                <div class="d-flex justify-content-between align-items-baseline">
                                                    <label>Email <span class="required">*</span></label>
                                                     <span  v-if="isSubmitted && !$v.form.email.emailValidation && $v.form.email.required" class="error-message">{{ errorMessageEmail() }}</span>
                                                </div>
                                                <input v-model="form.email" type="text"
                                                    class="form-control" :class="{ 'border-danger': isSubmitted && (!$v.form.email.required || !$v.form.email.emailValidation)}">
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
                                        <div class="col-md-6 d-flex justify-content-between">
                                           <div class="form-group w-49">
                                                <label>Ville <span class="required">*</span></label>
                                                <input v-model="form.ville" type="text"
                                                    class="form-control" :class="{ 'border-danger': isSubmitted && !$v.form.ville.required }">
                                               
                                            </div>
                                            <div class="form-group w-49">
                                                <label>Code Postal</label>
                                                <input v-model="form.cp" type="text" class="form-control">
                                               
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                           <div class="form-group max-country">
                                                <label>Pays <span class="required">*</span></label>
                                                <input v-model="form.pays" type="text"
                                                    class="form-control d-none"  :class="{ 'border-danger': isSubmitted && !$v.form.pays.required }">

                                                    <vue-country-dropdown
                                                        @onSelect="onSelect" :onlyCountries="countriesAuthorizedISO2()" :showNameInput="true">
                                                    </vue-country-dropdown>
                                               
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                           <div class="form-group">
                                                <div class="d-flex justify-content-between align-items-baseline">
                                                    <label>Téléphone Mobile <span class="required">*</span></label>
                                                    <span  v-if="isSubmitted && !$v.form.tel1.customTelValidation && $v.form.tel1.required" class="error-message">{{ errorMessageTelephone() }}</span>
                                                </div>
                                                <div class="d-flex">
                                                    <vue-country-dropdown
                                                        @onSelect="onSelectIndicatif1" :onlyCountries="countriesAuthorizedISO2()" :showNameInput="false" :enabledCountryCode="true">
                                                    </vue-country-dropdown>
                                                    <input v-model="form.tel1" type="text"
                                                    class="form-control ml-2" :class="{ 'border-danger': isSubmitted && (!$v.form.tel1.required || !$v.form.tel1.customTelValidation)}">
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="d-flex justify-content-between align-items-baseline">
                                                    <label>Téléphone Fixe</label>
                                                    <template v-if="form.tel2!=''">
                                                        <span  v-if="isSubmitted && !$v.form.tel2.customTelFixeValidation" class="error-message">{{ errorMessageTelephone() }}</span>
                                                    </template>

                                                </div>
                                                <div class="d-flex">
                                                    <vue-country-dropdown
                                                            @onSelect="onSelectIndicatif2" :onlyCountries="countriesAuthorizedISO2()" :showNameInput="false" :enabledCountryCode="true">
                                                        </vue-country-dropdown>
                                                    <input v-model="form.tel2" type="text"
                                                        class="form-control ml-2"  :class="{ 'border-danger': isSubmitted && !$v.form.tel2.customTelFixeValidation}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex"> 
                                <div class="orientation mr-3 text-left border-right pr-2">
                                    <label class="text-uppercase text-info titleform border-info pt-2">Piéce d'identité</label>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="row">
                                        <div class="col-md-6">
                                           <div class="form-group">
                                                <label>Type piéce <span class="required">*</span></label>
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
                                                <label>N°Piéce <span class="required">*</span></label>
                                                <input v-model="form.numero_piece" type="text"
                                                    class="form-control" :class="{ 'border-danger': isSubmitted && !$v.form.numero_piece.required }">
                                               
                                            </div>
                                        </div>
                                    </div>
                                     <div class="row">
                                        <div class="col-md-6">
                                           <div class="form-group d-flex justify-content-between flex-column">
                                                <label>Date expiration piéce</label>
                                                <date-picker v-model="form.date_expiration" class="w-100"  required valueType="YYYY-MM-DD" input-class="form-control w-100" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                           <div class="form-group">
                                                <label>Photo piéce</label>
                                                <input name="file" multiple type="file" ref="attachmentsPhotos" 
                                                    class="form-control border-0 pl-0" v-on:change="handleFileUpload()">
                                            </div>
                                            <div v-if="editmode">
                                                 <span v-for="photo in editKyc" class="mr-3 cursor-pointer" v-on:click="supprimerPhoto(photo, editKyc, form.id)">
                                                    <img :src="'/assets/kyc/'+photo" height="50" data-toggle="modal" data-target="#carouselPhoto" @click="setKyc(pro)"/>
                                                    <i class="text-danger fa fa-times"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!--div class="row">
                                <div class="col-md-12">
                                    <label class="text-uppercase text-info border-bottom titleform border-info">Infos Complémentaires</label>
                                </div>
                            </div-->
                            
                              
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button v-show="editmode" type="submit" class="btn btn-success" :disabled="isLoading ? true: false">Enregister</button>
                            <button v-show="editmode" type="button" class="btn btn-warning"  data-dismiss="modal" @click="reset()">Annuler</button>
                            <button v-show="!editmode" type="submit" class="btn btn-success" :disabled="isLoading ? true: false">Créer</button>
                            <!--button v-show="!editmode" type="button" class="btn btn-primary">Enregister comme brouillon</button-->
                            <button  v-show="!editmode" type="button" class="btn btn-info btn" @click="reset()">Réinitialiser</button>
                            <button  v-show="!editmode" type="button" class="btn btn-secondary btn" @click="reset()" data-dismiss="modal">Annuler</button>
                        </div>
                      </form>
                    </div>
                </div>
            </div>

            <!-- Modal details-->
            <div class="modal fade" id="moreInfo" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="moreInfo" aria-hidden="true">
                
                <div class="modal-dialog modal-xl position-relative" role="document">
                    <div class="modal-content">
                    <div class="modal-header align-items-center">
                        <h5 class="modal-title text-uppercase"><strong><span class="text-primary"><u>Info</u></span> Propriétaire</strong></h5>
                        <label class="ml-3 border border-lg py-1 px-3 mb-0 mr-2 bg-white">Date Ajout: <span class="text-info text-uppercase">{{ info.date_creation }}</span></label> 
                        <label class="ml-3 border border-lg py-1 px-3 mb-0 mr-2 bg-white">Date modifiée: <span class="text-info text-uppercase">{{ info.date_modif }}</span></label> 
                        <button type="button" class="close" ref="closePopupDetail" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 
                    <div class="modal-body modalinfo">
                            <div class="row">
                                <div class="col-md-6 d-flex justify-content-between">
                                    <div class="form-group w-49">
                                        <label>Nom</label>
                                        <span class="bg-light h4 px-2 d-block">{{ info.nom }}</span>
                                    </div>
                                    <div class="form-group w-49">
                                        <label>Prénom</label>
                                        <span class="bg-light h4 px-2 d-block">{{ info.prenom }}</span>
                                       
                                    </div>
                                </div>
                                <div class="col-md-6 d-flex justify-content-between">
                                     <div class="form-group w-49">
                                        <label>Nationalité</label>
                                        <span class="bg-light h4 px-2 d-block">{{ info.nationalite }}</span>
                                    </div>
                                     <div class="form-group max-country  w-49">
                                        <label>Profession</label>
                                       <span class="bg-light h4 px-2 d-block">{{ info.profession }}</span>
                                       
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 d-flex justify-content-between">
                                    <div class="form-group w-49">
                                        <label>Date de naissance</label>
                                        <span class="bg-light h4 px-2 d-block">{{ info.date_naissance }}</span>
                                    </div>
                                    <div class="form-group w-49">
                                        <label>Ville de naissance</label>
                                        <span class="bg-light h4 px-2 d-block">{{ info.ville_naissance }}</span>
                                       
                                    </div>
                                </div>
                                <div class="col-md-6 d-flex justify-content-between">
                                     <div class="form-group w-49">
                                        <label>Pays de naissance</label>
                                        <span class="bg-light h4 px-2 d-block">{{ info.pays_naissance }}</span>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <span class="bg-light h4 px-2 d-block">{{ info.email }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                   <div class="form-group">
                                    <label>Adresse</label>
                                        <span class="bg-light h4 px-2 d-block">{{ info.adresse }}</span>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 d-flex justify-content-between">
                                   <div class="form-group w-49">
                                        <label>Ville</label>
                                        <span class="bg-light h4 px-2 d-block">{{ info.ville }}</span>
                                       
                                    </div>
                                     <div class="form-group w-49">
                                        <label>Code Postal</label>
                                        <span class="bg-light h4 px-2 d-block">{{ info.cp }}</span>
                                       
                                    </div>
                                </div>
                                <div class="col-md-6">
                                   <div class="form-group max-country">
                                        <label>Pays</label>
                                        <span class="bg-light h4 px-2 d-block">{{ info.pays }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label>Téléphone Mobile</label>
                                        <span class="bg-light h4 px-2 d-block">({{ info.ind1 }}) {{ info.tel1 }}</span>    
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Téléphone Fixe</label>
                                        <span class="bg-light h4 px-2 d-block">({{ info.ind2 }}) {{ info.tel2 }}</span>  
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label>Type piéce</label>
                                        <span class="bg-light h4 px-2 d-block">{{ info.type_piece }}</span> 
                                    </div>
                                </div>
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label>N°Piéce</label>
                                        <span class="bg-light h4 px-2 d-block">{{ info.num_piece }}</span> 
                                       
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-6">
                                   <div class="form-group d-flex justify-content-between flex-column">
                                        <label>Date expiration piéce</label>
                                        <span class="bg-light h4 px-2 d-block">{{ info.date_expiration }}</span> 
                                    </div>
                                </div>
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label>Utilisateur</label>
                                        <span class="bg-light h4 px-2 d-block">{{ info.auteur }}</span> 
                                    </div>
                                </div>
                            </div>
                              <div class="row">
                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label>Entreprise</label>
                                        <span class="bg-light h4 px-2 d-block">{{ info.entreprise }}</span>  
                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Compte bancaire</label>
                                        <span class="bg-light h4 px-2 d-block">{{ info.compte_bancaire }}</span> 
                                    </div>
                                </div>
                            </div>
                              
                        </div>
                         <div class="modal-footer justify-content-center">
                            <button  type="button" class="btn btn-secondary btn btn-lg" data-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- modal carousel -->
            <modalCarousel></modalCarousel>
            <!-- Modal Badge-->
            <div  class="modal fade" id="badgeProprietaire" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="badgeLocataire" aria-hidden="true">
                <div class="modal-dialog modal-xl position-relative" role="document">
                    <div class="modal-content" >
                        <div class="modal-header">
                            <h5 class="modal-title text-uppercase">
                                <strong><span class="text-primary"><u>Badge</u></span> Proprietaire</strong>
                            </h5>

                            <button type="button" class="close" ref="closePopupBadge" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="background: #373737;">
                            <template v-if="proprietaireSelected && Object.keys(proprietaireSelected).length > 0">
                               <BadgeProprietaire
                                  :photo="'/assets/images/user.png'"
                                  :logo="'/assets/images/logo_abi_immo.png'"
                                  :qrCode="env+'/badge/proprietaire/'+proprietaireSelected.identifiant"
                                  :size="120"
                                  :proprietaire="{
                                    nom: proprietaireSelected.nom || '',
                                    prenom: proprietaireSelected.prenom || '',
                                    id: proprietaireSelected.identifiant || '',
                                    telephone: proprietaireSelected.ind1  +' '+proprietaireSelected.tel1,
                                    email: proprietaireSelected.email || '',
                                    pin: proprietaireSelected.pin || '',
                                  }"
                                />
                            </template>

                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-danger " @click="reset()" data-dismiss="modal">Fermer</button>
                        </div>

                    </div>
                </div>
            </div>

             <!-- Modal locataires actif-->
            <div  class="modal fade" id="modalLocataireActif" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="modalLocataireActif" aria-hidden="true">
                <div class="modal-dialog modal-xl position-relative" role="document">
                    <div class="modal-content" >
                        <div class="modal-header">
                            <h5 class="modal-title text-uppercase">
                                <strong><span class="text-primary"><u>Locataire</u></span> actif</strong>
                            </h5>

                            <button type="button" class="close" ref="closePopupBadge" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                             <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="bg-white">
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Téléphone</th>
                                        <th>Montant loyer</th>

                                    </thead>
                                    <tbody>
                                        <tr v-for="locataire in infosLocataire" :key="locataire.identifiant">
                                            <td>{{locataire.locat_nom}}</td>
                                            <td>{{locataire.locat_prenom}}</td>
                                            <td>{{locataire.locat_tel_1}}</td>
                                            <td>{{ helper_separator_amount(locataire.bail_montant_ht) }}</td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
                        </div>

                    </div>
                </div>
            </div>

             <!-- Modal bien loué-->
            <div  class="modal fade" id="modalBienLoue" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="modalBienLoue" aria-hidden="true">
                <div class="modal-dialog modal-xl position-relative" role="document">
                    <div class="modal-content" >
                        <div class="modal-header">
                            <h5 class="modal-title text-uppercase">
                                <strong><span class="text-primary"><u>Bien</u></span> loué</strong>
                            </h5>

                            <button type="button" class="close" ref="closePopupBadge" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                             <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="bg-white">
                                        <th>ID local</th>
                                        <th>Local loué</th>
                                        <th>Immeuble</th>
                                        <th>Adresse</th>
                                        <th class="text-right">Montant loyer</th>
                                    </thead>
                                    <tbody>
                                        <tr v-for="local in locauxLoue" :key="local.identifiantLocal">
                                            <th><span class="badge badge-info">{{ local.identifiantLocal }} </span></th>
                                            <td>{{ local.local_type_local }} ({{ local.local_type_location }})</td>
                                            <td>{{ local.bien_nom}}</td>
                                            <td>{{ local.bien_adresse }}, {{ local.bien_ville }} ({{ local.bien_pays }})</td>
                                            <td class="text-right">{{ helper_separator_amount(local.bail_montant_ht) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
                        </div>

                    </div>
                </div>
            </div>
    </template>
    <template v-else>
        <RepresentantSetup></RepresentantSetup>
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
import RepresentantSetup from './Representant.vue';
import BadgeProprietaire from '../badges/BadgeProprietaire.vue'
export default {
    name: "Proprietaire",
    props: ["listProprio", "env"],
    components: { DatePicker, VueCountryDropdown, modalCarousel, RepresentantSetup, BadgeProprietaire },
    data () {
        return {
            editmode: false,
            proprios : {},
            hasData: false,
            form: {
                id : '',
                nom : '',
                prenom: '',
                nationalite:'',
                profession: '',
                ville_naissance: '',
                indicatif1: '',
                tel1: '',
                indicatif2: '',
                tel2:  '',
                email: '',
                adresse: '',
                ville: '',
                pays: '',
                entreprise: '',
                compte_bancaire:'',
                type_piece: '',
                numero_piece: '',
                date_expiration:'',
                photo_piece: null,
                date_naissance: '',
                pays_naissance:'',
                cp: ''
            },
            attachmentsPhotos: [],
            info: {},
            isSubmitted: false,
            isLoading: false,
            paginate: 5,
            editKyc: [],
            isLoadingTab: false,
            viewRepresentant: false,
            proprioSelected: null,
            proprioID: '',
            proprietaireSelected: {},
            infosLocataire: [],
            locauxLoue: []
        }
    },
    validations: {
        form : {
            nom:     { required },
            prenom:  { required },
            tel1:    { required,
                customTelValidation(value) {
                   const regex = this.getRegexForCountryTelMobile(this.form.indicatif1);
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
            tel2:    {
                customTelFixeValidation(value) {
                    if(value=='') return true;
                   const regex = this.getRegexForCountryTelFixe(this.form.indicatif2);
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
            email:   { required,
                emailValidation(value) {
                    const regex = this.getRegexEmail();
                    console.log(">>>", regex.test(value))
                    return regex.test(value);
                }
            },
            adresse: { required },
            ville:   { required },
            pays:    { required },
            ville_naissance: { required },
            profession: { required },
            nationalite: { required },
            type_piece: { required },
            numero_piece: { required }
        }
    },
    watch: {
       paginate: function(){
            this.getProprio();
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
            data.append('nom', this.form.nom);
            data.append('prenom', this.form.prenom);
            data.append('ville_naissance', this.form.ville_naissance);
            data.append('profession', this.form.profession);
            data.append('nationalite', this.form.nationalite);
            data.append('email', this.form.email);
            data.append('indicatif1', this.form.indicatif1);
            data.append('tel1', this.form.tel1); 
            data.append('indicatif2', this.form.indicatif2);
            data.append('tel2', this.form.tel2);
            data.append('adresse', this.form.adresse);
            data.append('ville', this.form.ville);
            data.append('pays', this.form.pays);
            data.append('compte_bancaire', this.form.compte_bancaire);
            data.append('type_piece', this.form.type_piece);
            data.append('num_piece', this.form.numero_piece);
            data.append('date_expiration', this.form.date_expiration);
            data.append('date_naissance', this.form.date_naissance);
            data.append('pays_naissance', this.form.pays_naissance);
            data.append('cp', this.form.cp);
         
            data.append('entreprise', this.form.entreprise);
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

     
            axios.post("/proprio/"+action, data,  {
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
                      action=='create'? 'Propriétaire crée avec succés!':'Propriétaire modifié avec succés!',
                      'success'
                    );

                    this.getProprio();

                }else{
                     Vue.swal.fire(
                      'error!',
                      response.data.message,
                      'error'
                    )
                }
                this.isSubmitted = false;

            }).catch(error => {
                this.isLoading = false;
                this.isSubmitted = false;

                // 🔥 Gestion des erreurs serveurs
                Vue.swal.fire(
                    'Erreur serveur',
                    error.response?.data?.message || 'Erreur inattendue. Veuillez réessayer.',
                    'error'
                );

                console.error('Erreur Axios :', error);
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
        onSelectNaissance({name, iso2, dialCode}) {
           console.log(name, iso2, dialCode);
           this.form.pays_naissance = iso2; 
        },
        newModal(){
          this.editmode = false;
        },
        getProprio(page=1){
            this.isLoadingTab = true;
            const params = {};
            if (this.proprioID) params.proprioID = this.proprioID;
            axios.get("/proprio/listing?paginate="+ this.paginate+'&page=' + page, {params}).then(responses => {

              console.log(responses);
              this.isLoadingTab = false;

              this.proprios = responses.data;

              if(this.proprios.data.length == 0){
                this.hasData = false;
              }
              else{
                 this.hasData = true;
              }



            }).catch(errors => { 

            // react on errors.

            })
        },
        reset(){
            this.isSubmitted = false;
            this.form.id = '';
            this.form.nom = '';
            this.form.prenom = '';
            this.form.nationalite = '';
            this.form.profession = '';
            this.form.ville_naissance = '';
            this.form.tel1 = '';
            this.form.tel2 = '';
            this.form.email = '';
            this.form.adresse = '';
            this.form.ville = '';
            //this.form.pays = '';
            this.form.entreprise = '';
            this.form.compte_bancaire = '';
            this.form.type_piece = '';
            this.form.numero_piece = '';
            this.form.date_expiration = '';
            this.form.photo_piece = '';
            this.attachmentsPhotos = [];
            this.$refs.attachmentsPhotos.value = null;
            this.form.date_naissance ='';
            this.form.pays_naissance = '';
            this.form.cp = '';
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
            this.form.nom = current.nom;
            this.form.nationalite = current.nationalite;
            this.form.ville_naissance = current.ville_naissance;
            this.form.profession = current.profession;
            this.form.prenom = current.prenom;
            this.form.tel1 = current.tel1;
            this.form.tel2 = current.tel2;
            this.form.email = current.email;
            this.form.adresse = current.adresse;
            this.form.ville = current.ville;
            this.form.pays = current.pays;
            this.form.entreprise = current.entreprise;
            this.form.compte_bancaire = current.compte_bancaire;
            this.form.type_piece = current.type_piece;
            this.form.numero_piece = current.num_piece;
            this.form.date_expiration = current.date_expiration_natif;
            this.form.photo_piece = current.photo_piece;
            this.editKyc = current.kyc;
            this.form.date_naissance = current.date_naiss_natif;
            this.form.pays_naissance = current.pays_naissance;
            this.form.cp = current.cp;
        },
        setKyc(prop){
             // get photo
            EventBus.$emit('VIEW_CAROUSEL', { 
                kyc: prop.kyc,
                path: '/assets/kyc/'
            });
        },
        view(prop){
            this.info = prop;
            this.infosLocataire =  prop.locatairesActifs;
            this.locauxLoue     = prop.locauxloue;
            console.log("locat>>", this.infosLocataire)
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
                axios.post('/proprio/remphoto', data).then(response => {
                    console.log(response);
                    if(response.data.code==0){
                         Vue.swal.fire(
                          'Suppression',
                          'Photo supprimée avec succés',
                          'success'
                        );
                        this.editKyc = response.data.file;
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
                        this.getProprio();  
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
        setUpRepresentant(proprio){

            this.viewRepresentant = true;

            setTimeout(function(){
                EventBus.$emit('VIEW_REPRESENTANT', { 
                    current: proprio
                });
            }, 200);
            
        },
        showBadge(item) {
            this.proprietaireSelected = item;
            console.log(">>>>", this.proprietaireSelected)
        },
        onInputSelectProprio(value) {
          if (!value) {
            this.proprioSelected = null;
            this.proprioID = '';
            this.getProprio(); // 💡 appel de ton action de réinitialisation
          }
        },
        onProprioChoisi(proprio){
          this.proprioSelected = proprio;
          this.proprioID = proprio.proprio_id;
          this.getProprio();
        }

    },
    mounted() {
        this.listProprio.map(function (x){
          return x.item_data = x.proprio_nom + ' ' + x.proprio_prenom + ' (' +x.proprio_id +')';
        }); this.listProprio.map(function (x){
          return x.item_data = x.proprio_nom + ' ' + x.proprio_prenom + ' (' +x.proprio_id +')';
        });
        this.getProprio();
         EventBus.$on('BACK', (event) => {
          this.viewRepresentant = event.back;
          this.getProprio();
        });        
    }
}
</script>
<style>
.modal.modal-fullscreen {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    margin: 0;
    padding: 0;
    width: 100vw;
    height: 100vh;

    display: block;
    overflow: hidden;
}

.modal-dialog.modal-fullscreen {
    margin: 0;
    width: 100%;
    height: 100%;
    max-width: 100%;
}

.modal-content {
    height: 100%;
    display: flex;
    flex-direction: column;
}
.modal-body {
    overflow-y: auto;
    flex: 1;
}
</style>
