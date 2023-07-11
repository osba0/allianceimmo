<template>
    <div>
        <!-- Modal Carousel-->
        <div class="modal fullscreenModal fade" id="modalFichier" data-backdrop="static" data-keyboard="false" tabindex="-1"  role="dialog" aria-labelledby="modalFichier" aria-hidden="true">
            
            <div class="modal-dialog modal-xl position-relative" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-uppercase">{{title}}</h5>
                        <button type="button" class="close" ref="closePopup" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 
                    <div class="modal-body">
                        <template v-if="path!=''">
                                <embed :src="'/'+path" frameborder="0" width="100%" height="500px">
                              </template>
                         <template v-else>
                            <div class="w-100 text-center">Aucun Document</div> 
                        </template>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    </div>
                 
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
           title: '',
           path:''
        }
      },
      methods: {
       
      },
         
      mounted() {
          EventBus.$on('VIEW_DOCUMENT', (event) => {
              this.title = event.title;
              this.path = event.path;
          });
      }
  }
</script> 
