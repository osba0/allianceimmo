<template>
	<!-- Modal details-->
    <div class="modal fade" id="moreInfo" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="moreInfo" aria-hidden="true">
            
        <div class="modal-dialog modal-xl position-relative" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-uppercase"><strong><span class="text-primary"><u>Info</u></span> Agence </strong></h5>
                <button type="button" class="close" ref="closePopupDetail" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <div class="modal-body modalinfo">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nom</label>
                                <span class="bg-light h4 px-2 d-block">{{ info.proprio }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex justify-content-between">
                            <div class="form-group w-49">
                                <label>Slogan</label>
                                <span class="bg-light h4 px-2 d-block">{{ info.nom_immeuble }}</span>
                               
                            </div>
                            <div class="form-group w-49">
                                <label>N°Immeuble</label>
                                <span class="bg-light h4 px-2 d-block">{{ info.numero }}</span>
                               
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Etage</label>
                                <span class="bg-light h4 px-2 d-block">{{ info.etage }}</span>
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
                        <div class="col-md-6">
                           <div class="form-group">
                                <label>Ville</label>
                                <span class="bg-light h4 px-2 d-block">{{ info.ville }}</span>
                               
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
                                <label>Superficie m<sup>2</sup></label>
                                <span class="bg-light h4 px-2 d-block">{{ info.superficie }}</span>    
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Année de construction</label>
                                <span class="bg-light h4 px-2 d-block">{{ info.annee_construction }}</span>  
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                                <label>Description</label>
                                <span class="bg-light h4 px-2 d-block">{{ info.description }}</span>  
                                
                            </div>
                        </div>
                        <div class="col-md-6">
                           
                        </div>
                    </div>
                      
                </div>
                 <div class="modal-footer justify-content-center">
                    <button  type="button" class="btn btn-secondary btn btn-lg" data-dismiss="modal">Fermer</button>
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
          EventBus.$on('VIEW_INFO', (event) => {
              this.info = event.info;
          });
      }
  }
</script> 
