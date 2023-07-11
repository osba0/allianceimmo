<template>
    <div>
        <!-- Modal Carousel-->
        <div class="modal fade" id="carouselPhoto" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-body">
                   <!-- carousel -->
                  <div id='carouselExampleIndicators'
                       class='carousel slide'
                       data-ride='carousel'
                       >
                        <ol class='carousel-indicators'>
                          <li v-for="(item,index) in kyc" data-target='#carouselExampleIndicators'
                              :data-slide-to='index'
                              :class="[index==0? 'active':'']"></li>
                        </ol>
                        <div class='carousel-inner'>
                          <div class='carousel-item text-center' v-for="(item,index) in kyc" :class="[index==0? 'active':'']">
                            <img class='img-size' :src="path+item" alt='' style="max-height: 900px;"/>
                          </div>
                        </div>
                        <a
                           class='carousel-control-prev'
                           href='#carouselExampleIndicators'
                           role='button'
                           data-slide='prev'
                           >
                          <span class='carousel-control-prev-icon-osba'
                                aria-hidden='true'
                                >←</span>
                          <span class='sr-only'>Previous</span>
                        </a>
                        <a
                           class='carousel-control-next'
                           href='#carouselExampleIndicators'
                           role='button'
                           data-slide='next'
                           >
                          <span
                                class='carousel-control-next-icon-osba'
                                aria-hidden='true'
                                >→</span>
                          <span class='sr-only'>Next</span> 
                        </a>
                  </div>
                </div>
                <div class="modal-footer justify-content-center">
                  <button type="button" class="btn btn-warning" data-dismiss="modal">Fermer</button>
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
           kyc: [],
           path:''
        }
      },
      methods: {
       
      },
         
      mounted() {
          EventBus.$on('VIEW_CAROUSEL', (event) => {
              this.kyc = event.kyc;
              this.path = event.path;
          });
      }
  }
</script> 
