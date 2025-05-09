<template>
    <div>
        <!-- Modal File-->
        <div class="modal fade" id="openFile" tabindex="-1" role="dialog" aria-labelledby="myModalFacturePops"
          aria-hidden="true" data-backdrop="static" data-keyboard="false">
          <div class="modal-dialog modal-xl" role="document">
          	 <div class="modal-content">
          	 		<div class="modal-header text-left">
                        <h4 class="modal-title w-100 font-weight-bold">{{ title || 'Infos' }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" ref="closeModalInfo">
                          <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body mx-3">
                  	    {{texte}}
                    </div>
                    <div class="modal-footer d-flex justify-content-center"> 
                      <!-- Slot pour bouton personnalisé -->
                    <slot name="footer-action"></slot>
                    <button type="button" v-on:click="closeModalInfo()" class="btn btn-warning">Fermer</button>
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
           texte: '',
           title: ''
        }
      },
      methods: {
        closeModalInfo(){
             this.$refs.closeModalInfo.click();
             this.texte=null;
        }
       
      },
         
      mounted() {

          EventBus.$on('VIEW_TEXT', (event) => {
              this.texte = event.texte;
              this.title = event.title;
              const modalElement = document.getElementById('openFile');
              const modal = new Modal(modalElement);
              modal.show();
          });
      }
  }
</script> 
