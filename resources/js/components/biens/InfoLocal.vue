<template>
	<!-- Modal details-->
    <div class="modal fade" id="moreInfo" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="moreInfo" aria-hidden="true">
            
        <div class="modal-dialog modal-xl position-relative" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-uppercase"><strong><span class="text-primary"><u>Info</u></span> Local</strong></h5>
                <button type="button" class="close" ref="closePopupDetail" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <div class="modal-body modalinfo">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type Local</label>
                                <span class="bg-light h4 px-2 d-block">{{ info.type_local }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type de Location proposé</label>
                                <span class="bg-light h4 px-2 d-block">{{ info.type_location }}</span>
                               
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Prix du Loyer hors charge</label>
                                <span class="bg-light h4 px-2 d-block">{{ info.prix_loyer }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                            <label>Montant des charges</label>
                                <span class="bg-light h4 px-2 d-block">{{ info.montant_charge }}</span>
                                
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                                <label>Superficie en m2</label>
                                <span class="bg-light h4 px-2 d-block">{{ info.superficie }}</span>
                               
                            </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group max-country">
                                <label>Nombre de piéces</label>
                                <span class="bg-light h4 px-2 d-block">{{ info.nombre_piece }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                                <label>Salle de Bain</label>
                                <span class="bg-light h4 px-2 d-block">{{ info.salle_bain }}</span>    
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
                            <label>Agent</label>
                            <span class="bg-light h4 px-2 d-block">{{ info.user }}</span> 
                        </div>
                    </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                                <label>Date Ajout</label>
                                <span class="bg-light h4 px-2 d-block">{{ info.date_creation }}</span>  
                                
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Date Mofication</label>
                            <span class="bg-light h4 px-2 d-block">{{ info.date_modif }}</span> 
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
          EventBus.$on('VIEW_INFO_LOCAL', (event) => {
              this.info = event.info;
          });
      }
  }
</script> 
