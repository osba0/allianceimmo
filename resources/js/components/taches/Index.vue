<template>
  <div>
      <div class="row">
        <div class="col-md-4" v-for="task in tasks" :key="task.id">
          <div class="card border h-100">
            <div class="card-body d-flex flex-column justify-content-between">
              <div>
                <div class="mb-3">
                  <i :class="task.icon" class="fa-3x text-primary"></i>
                </div>
                <h5 class="card-title font-weight-bold">{{ task.title }}</h5>
                <p class="card-text small text-muted">{{ task.description }}</p>
              </div>
              <button :disabled="task.loading" class="btn btn-outline-primary btn-block mt-3" @click="runTask(task)">
                <span v-if="task.loading" class="spinner-border spinner-border-sm mr-1"></span>
                {{ task.button }}
              </button>
            </div>
          </div>
        </div>
      </div>

  </div>
</template>

<script>
import axios from "axios";
import moment from "moment";

export default {
  data() {
    return {
       tasks: [
        {
          id: 'loyers',
          title: 'Générer les loyers',
          description: 'Créer automatiquement les loyers mensuels des baux actifs.',
          button: 'Lancer la génération',
          icon: 'fas fa-file-invoice-dollar',
          command: 'command:loyers',
          loading: false,
          endpoint: '/commands/generate-loyers'
        },
        {
          id: 'emails',
          title: 'Envoyer les mails',
          description: 'Déclencher l’envoi de tous les mails en attente (versements, alertes, etc.).',
          button: 'Envoyer les mails',
          icon: 'fas fa-envelope',
          command: 'emails:envoyer',
          loading: false,
          endpoint: '/commands/send-mails'
        },
        {
          id: 'mandats',
          title: 'Vérifier les mandats',
          description: 'Contrôler la validité des mandats et les désactiver si expirés.',
          button: 'Vérifier les mandats',
          icon: 'fas fa-calendar-check',
          command: 'command:verifier-expiration-mandat',
          loading: false,
          endpoint: '/commands/check-mandats'
        }
      ]
    };
  },
  watch: {


  },
  methods: {
     runTask(task) {
      task.loading = true;
      axios.post(task.endpoint,  {command: task.command})
        .then(res => {
          Vue.swal.fire(
            res.data.message,
            res.data.output,
            'info'
          );
        })
        .catch(() => {
          Vue.swal.fire(
            'Error',
            'Une erreur est survenue lors de l’exécution',
            'error'
          );
        })
        .finally(() => task.loading = false);
    },
    genererLoyers(commande) {
      axios.post('/commands/generate-loyers', {command: commande}).then(res => {
         Vue.swal.fire(
            res.data.message,
            res.data.output,
            'info'
          );
      }).catch(() => {
        this.$toast.error('Erreur lors de l’exécution');
      });
    }
  },
  mounted() {

  }
};
</script>

<style scoped>
.table th, .table td {
  vertical-align: middle;
}
</style>
