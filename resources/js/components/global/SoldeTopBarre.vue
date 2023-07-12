<template>
    <div class="d-flex align-items-center">
        <span class="badge badge-primary ml-3 mr-2">SOLDE</span> 
        <template v-if="visible">
             <button  @click="showHide()" title="Masquer le solde" class="text-primary h3 mb-0 ml-1 mr-2 bg-transparent border-0"><i class="fas fa-eye"></i></button> 
        </template>
        <template v-else>
             <button  @click="showHide()" title="Afficher le solde" class="text-primary h3 mb-0 ml-1 mr-2 bg-transparent border-0"><i class="fas fa-eye-slash"></i></button> 
        </template>
       


       
        <span class="text-info h4 mb-0 font-weight-bold">
            <template v-if="visible">
                {{ solde }}
            </template>
            <template v-else>
                -------------
             </template>
        </span>
        <button v-if="visible" title="Actualiser le solde" class="text-primary h4 mb-0 ml-3 mr-2 rounded-circle bg-white"><i class="fas fa-sync-alt"></i></button>  
    </div>
</template>

<script>
export default {
    name: "soldeTopBarre",
    data() { 
        return {
          visible: false,
          solde : '47.384.972 FCFA'
        }
      },
      methods: { 
       showHide(){
        this.visible = !this.visible;
       }
      },
      mounted() {
        this.visible = false;
      }

}
</script>
