<template>
    <div>
        <!-- Modal File-->
        <div class="modal fade fullscreenModal" id="openFile" tabindex="-1" role="dialog" aria-labelledby="myModalFacturePops"
          aria-hidden="true" data-backdrop="static" data-keyboard="false">
          <div class="modal-dialog modal-xl" role="document">
          	 <div class="modal-content">
          	 	
          	 		<div class="modal-header text-left">
                        <h4 class="modal-title w-100 font-weight-bold">File</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" ref="closeModalFile">
                          <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body mx-3">
                  	     <template v-if="pdfFile != null">
                         	<embed :src="pdfFile" frameborder="0" width="100%" height="450px">
                     	  </template>
                 	     <template  v-else> Auncun fichier </template>
                    </div>
                    <div class="modal-footer d-flex justify-content-center"> 
                      <!-- Slot pour bouton personnalisé -->
                    <slot name="footer-action"></slot>
                    <button type="button" v-on:click="closeModalPdf()" class="btn btn-warning">Fermer</button>
                  </div>
          	 </div>
            
          </div>
        </div>
       
    </div>
</template>

<script>
  import { EventBus } from '../../event-bus';
  import { Modal } from 'bootstrap';
export default {
    props: [],
     components: {
       
      },
      data() { 
        return {
           pdfFile: null,
        }
      },
      methods: {
        closeModalPdf(){
             this.$refs.closeModalFile.click();
             this.pdfFile=null;
        }
       
      },
         
      mounted() {

          EventBus.$on('VIEW_FILE', (event) => {
              this.pdfFile = event.pathFile;
              const modalElement = document.getElementById('openFile');
              const modal = new Modal(modalElement);
              modal.show();
             // $('#openFile').modal('show');
          });
      }
  }
</script> 
