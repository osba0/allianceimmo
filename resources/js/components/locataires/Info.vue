<template>
	<!-- Modal details-->
    <div class="modal fade" id="moreInfo" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="moreInfo" aria-hidden="true">
            
        <div class="modal-dialog modal-xl position-relative" role="document">
            <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title text-uppercase"><strong><span class="text-primary"><u>Info</u></span> Locataire</strong></h5>
                <label class="ml-3 border border-lg py-1 px-3 mb-0 mr-2 bg-white">Date ajout: <span class="text-info text-uppercase">{{ info.date_creation }}</span></label> 
                        <label class="ml-3 border border-lg py-1 px-3 mb-0 mr-2 bg-white">Date modifiée: <span class="text-info text-uppercase">{{ info.date_modif }}</span></label> 
                <button type="button" class="close" ref="closePopupDetail" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <div class="modal-body modalinfo">
                <div class="d-flex">
                    <div class="orientation mr-3 text-left border-right pr-2">
                        <label class="text-uppercase text-info titleform border-info pt-2">Infos Perso / Infos Société</label>
                    </div>
                    <div class="flex-grow-1">
                        <div class="row">
                            <div class="col-md-6 d-flex justify-content-between">
                                 <div class="form-group w-49">
                                    <label>Civilité</label>
                                    <span class="bg-light h4 px-2 d-block">{{ info.civilite }}</span>
                                </div>
                                <div class="form-group w-49">
                                    <label>Type Locataire</label>
                                    <span class="bg-light h4 px-2 d-block">{{ info.type_location }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex justify-content-between">
                               <div class="form-group w-49">
                                    <label>Nom</label>
                                    <span class="bg-light h4 px-2 d-block">{{ info.nom }}</span>
                                </div>
                                 <div class="form-group max-country  w-49">
                                    <label>Prénom</label>
                                    <span class="bg-light h4 px-2 d-block">{{ info.prenom }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 d-flex justify-content-between">
                                <div class="form-group w-49">
                                     <label>Date de naissance</label>
                                     <span class="bg-light h4 px-2 d-block">{{ info.date_naissance }}</span>
                                    
                                </div>
                                <div class="form-group w-49 max-country"> 
                                    <label>Pays de naissance</label>
                                   <span class="bg-light h4 px-2 d-block">{{ info.pays_naissance }}</span>
                                   
                                </div>
                            </div>
                            <div class="col-md-6 d-flex justify-content-between">
                               <div class="form-group w-49">
                                    <label>Société</label>
                                    <span class="bg-light h4 px-2 d-block">{{ info.societe }}</span>
                                </div>
                                 <div class="form-group max-country  w-49">
                                    <label>N° TVA</label>
                                    <span class="bg-light h4 px-2 d-block">{{ info.num_tva }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 d-flex justify-content-between">
                               <div class="form-group w-49">
                                    <label>Ninea / Registre Commerce</label>
                                    <span class="bg-light h4 px-2 d-block">{{ info.ninea_rc }}</span>
                                </div>
                                 <div class="form-group max-country  w-49">
                                    <label>Domaine d'Activité</label>
                                    <span class="bg-light h4 px-2 d-block">{{ info.domaine_activite }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Photo</label>
                                    <span class="bg-light h4 px-2 d-block">Avoir</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                               <div class="form-group">
                                    <label>Profession</label>
                                    <span class="bg-light h4 px-2 d-block">{{ info.profession }}</span>
                                   
                                </div>
                            </div>
                            <div class="col-md-4">
                               <div class="form-group">
                                    <label>Revenus mensuels</label>
                                    <span class="bg-light h4 px-2 d-block">{{ info.revenu_mensuel }}</span>
                                   
                                </div>
                            </div>
                             <div class="col-md-4">
                               <div class="form-group">
                                    <label>Justificatif de Revenus</label>
                                    <span class="bg-light h4 px-2 d-block">{{ info.justificatif_revenu }}</span>
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
                                    <label>Téléphone Mobile</label>
                                    <span class="bg-light h4 px-2 d-block">{{ info.ind1 }} {{ info.tel1 }}</span>
                                    
                                </div>
                                 <div class="form-group  w-49">
                                    <label>Téléphone Fixe</label>
                                    <span class="bg-light h4 px-2 d-block">{{ info.ind2 }} {{ info.tel2 }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex justify-content-between">
                               <div class="form-group w-49">
                                    <label>Email</label>
                                    <span class="bg-light h4 px-2 d-block">{{ info.email }}</span>
                                   
                                </div>
                                 <div class="form-group w-49">
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
                                 <div class="form-group  w-49">
                                    <label>Région</label>
                                    <span class="bg-light h4 px-2 d-block">{{ info.region }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex justify-content-between">
                               <div class="form-group w-49">
                                    <label>Code Postal</label>
                                    <span class="bg-light h4 px-2 d-block">{{ info.cp }}</span>
                                   
                                </div>
                                 <div class="form-group w-49 max-country">
                                    <label>Pays</label>
                                    <span class="bg-light h4 px-2 d-block">{{ info.pays }}</span>
                                   
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
                                    <label>Photo piéce</label>
                                    <span class="bg-light h4 px-2 d-block">A voir</span>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
                      
                </div>
                 <div class="modal-footer justify-content-center">
                    <button  type="button" class="btn btn-secondary btn" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { EventBus } from '../../event-bus';
export default {
    props: [],
     components: {
       
      },
      data() { 
        return {
           info: {},
           path:''
        }
      },
      methods: {
       
      },
      mounted() {
          EventBus.$on('VIEW_INFO_LOCATAIRE', (event) => {
              this.info = event.info;
          });
      }
  }
</script> 
