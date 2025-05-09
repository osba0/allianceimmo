<template>
  <div>
    <h3>📄 Rapport des loyers</h3>

    <div class="filters">
      <input v-model="filtre.locataire" placeholder="ID locataire..." />
      <input v-model="filtre.proprietaire" placeholder="ID propriétaire..." />
      <input v-model="filtre.mois" type="number" min="1" max="12" placeholder="Mois" />
      <input v-model="filtre.annee" type="number" placeholder="Année" />
      <button @click="chargerRapports">Charger</button>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>Locataire</th>
          <th>Propriétaire</th>
          <th>Bien</th>
          <th>Local</th>
          <th>Montant</th>
          <th>Date Paiement</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="r in rapports" :key="r.paiement_id">
          <td>{{ r.locat_nom }} {{ r.locat_prenom }}</td>
          <td>{{ r.prop_nom }}</td>
          <td>{{ r.bien_adresse }}</td>
          <td>{{ r.local_type_local }}</td>
          <td>{{ r.paiement_montant }} F</td>
          <td>{{ r.created_at | formatDate }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      rapports: [],
      filtre: {
        locataire: '',
        proprietaire: '',
        mois: '',
        annee: ''
      }
    }
  },
  methods: {
    async chargerRapports() {
      const { data } = await axios.get('/api/rapports/loyers', { params: this.filtre })
      this.rapports = data.data
    }
  },
  filters: {
    formatDate(value) {
      return new Date(value).toLocaleString()
    }
  }
}
</script>
