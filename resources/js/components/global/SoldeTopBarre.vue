<template>
    <div class="d-flex align-items-center">
        <span class="badge badge-primary ml-3 mr-2">SOLDE</span> 
        <template v-if="visible">
             <button  @click="showHide()" title="Masquer le solde" class="text-primary h3 mb-0 ml-1 mr-2 bg-transparent border-0"><i class="fas fa-eye-slash"></i></button>
        </template>
        <template v-else>
             <button  @click="showHide()" title="Afficher le solde" class="text-primary h3 mb-0 ml-1 mr-2 bg-transparent border-0"><i class="fas fa-eye"></i></button>
        </template>
       


       
        <span class="text-info h4 mb-0 font-weight-bold">
            <template v-if="visible">
                {{ helper_separator_amount(solde) }} {{ solde > 0? "FCFA":""}}
            </template>
            <template v-else>
                -------------
             </template>
        </span>
        <button v-if="visible" @click="getSolde()" title="Actualiser le solde" class="text-primary h4 border-0 mb-0 ml-3 mr-2 rounded-circle bg-white" :class="isLoading?'animateBtn':''"><i class="fas fa-sync-alt"></i></button>
    </div>
</template>

<script>
export default {
    name: "soldeTopBarre",
    data() { 
        return {
          visible: false,
          solde : 0,
          isLoading: false
        }
      },
      methods: { 
       showHide(){
        this.visible = !this.visible;
       },
       getSolde() {
         this.isLoading = true;
            axios.get("/global/getsolde").then(responses => {
                console.log(responses);
                this.solde = responses.data.solde;
                var self = this;
                setTimeout(function(){
                    self.isLoading = false;
                },1500)

            }).catch(errors => {
                this.isLoading = true;
                 // react on errors.

            })
       }
      },
      mounted() {
        this.visible = false;
        this.getSolde();
      }

}
</script>
<style scoped>
    .animateBtn{
        animation: spin 1s infinite linear;
    }
    /* Animation de rotation */
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>
