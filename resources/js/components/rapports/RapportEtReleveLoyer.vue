<template>
  <div>
    <!-- Onglets -->
    <ul class="nav nav-tabs mb-3 tab-rapport">
      <li class="nav-item">
        <a class="nav-link" :class="{ active: activeTab === 'locataires' }" @click="activeTab = 'locataires'">Locataires</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" :class="{ active: activeTab === 'proprietaires' }" @click="activeTab = 'proprietaires'">Propriétaires</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" :class="{ active: activeTab === 'agence' }" @click="activeTab = 'agence'">Agence</a>
      </li>
    </ul>

    <!-- Contenu -->
    <div v-if="activeTab === 'locataires'">
        <h4 class="text-center headerRapport">Rapport des Locataires</h4>
        <div class="d-flex align-items-center justify-content-between">
            <div class="flex-1 pr-5">
            <!-- Filtres pour les dates -->
              <div class="d-flex justify-content-between align-items-end gap-15 mb-3">
                <div class="d-flex align-items-end gap-15">
                    <div style="width: 300px">
                      <label for="fin" class="mr-2">Locataire :</label>
                        <v-select v-model="locataireSelected" @input="onInputSelectLocataire" placeholder="Filtrer par locataire" :options="listLocataire"  @option:selected="onLocataireChoisi" label="item_data"></v-select>
                    </div>
                    <div>
                      <label for="debut" class="mr-2">Date début :</label>
                      <date-picker v-model="debut" class="w-100 mr-4"  required valueType="YYYY-MM-DD" input-class="form-control w-100" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>
                    </div>
                    <div>
                      <label for="fin" class="mr-2">Date fin :</label>
                       <date-picker v-model="fin" class="w-100 mr-4"  required valueType="YYYY-MM-DD" input-class="form-control w-100" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>

                    </div>
                    <button class="btn btn-primary" @click="fetchLocataires">Filtrer</button>
                </div>
                <div v-if="locataireSelected != null && debut!= '' && fin !=''">
                  <button class="btn btn-md btn-success" @click="generationRapportLocataire"><i class="fa fa-eye"></i> Voir le Rapport</button>
                </div>
              </div>

              <div>
                 <table class="table table-bordered text-center mb-3 mt-3">
                  <thead>
                    <tr>
                      <th>🧾 Nombre de ligne</th>
                      <th>👥 Locataires distincts</th>
                      <th>❌ Total impayé</th>
                      <th>🎯 Total encaissé</th>

                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><label class="badge badge-info">{{ totalLignes }}</label></td>
                      <td><label class="badge badge-primary">{{ totalLocataires }}</label></td>
                      <td><label class="badge badge-danger">{{ formatMontant(montantImpayes) }}</label></td>
                      <td><label class="badge badge-success">{{ totalGeneral }}</label></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          <div v-if="pieChartData">
             <Pie :data="pieChartData" :options="pieChartOptions" style="max-width: 300px; margin: auto" />
          </div>
        </div>

        <div>
           <table class="table table-hover table-striped">
            <thead>
              <tr>
                <th>Nom Locataire</th>

                <th>Adresse Bien</th>
                <th>Mois</th>
                <th class="align-right">Montant Location</th>
                <th class="align-right">Total payé</th>
                <th class="text-center">Statut</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in locatairesData.data" :key="item.paiement_id">
                <td>{{ item.locat_nom }} {{ item.locat_prenom }}</td>
                <td>{{ item.bien_adresse }}</td>
                <td>{{ item.paiement_mois_location }}</td>
                <td class="align-right">{{ formatMontant(item.paiement_montant) }}</td>
                <td class="align-right">{{ formatMontant(getTotalPaiement(item.paiement_recu)) }}</td>
                <td class="text-center">{{ getStatutPaiement(item.paiement_etat) }}</td>
              </tr>
            </tbody>

          </table>

      </div>
      <div class="d-flex mt-4 justify-content-center">
            <pagination
                :data="paginationMeta"
                :limit=10
                @pagination-change-page="fetchLocataires"
            ></pagination>
        </div>
    </div>

    <div v-if="activeTab === 'proprietaires'">
      <h4 class="text-center headerRapport">DETAILS ENCAISSEMENTS DES LOYERS</h4>

        <!-- Filtres pour les dates -->
        <div class="d-flex justify-content-between align-items-end gap-15 mb-3">
          <div class="d-flex align-items-end gap-15">
              <div style="width: 300px">
                <label for="fin" class="mr-2">Propriétaire :</label>
                  <v-select v-model="proprioSelected" @input="onInputSelectProprio" placeholder="Filtrer par propriétaire" :options="listProprio"  @option:selected="onProprioChoisi" label="item_data"></v-select>
              </div>
              <div>
                <label for="debut" class="mr-2">Date début :</label>
                <date-picker v-model="debut" class="w-100 mr-4"  required valueType="YYYY-MM-DD" input-class="form-control w-100" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>
              </div>
              <div>
                <label for="fin" class="mr-2">Date fin :</label>
                 <date-picker v-model="fin" class="w-100 mr-4"  required valueType="YYYY-MM-DD" input-class="form-control w-100" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>

              </div>
              <button class="btn btn-primary" @click="fetchProprietaires">Filtrer</button>
          </div>
          <div v-if="proprioSelected != null && debut!= '' && fin !=''">
            <button class="btn btn-md btn-success" @click="generationEncaissementLoyer"><i class="fa fa-eye"></i> Voir le Rapport</button>
          </div>
        </div>
        <div style="height: 30vh"><Bar :data="barChartData" :options="barChartOptions"/></div>
        <div class="table-responsive" style="height: 35vh">
         <table class="table table-hover table-striped">
          <thead>
            <tr>
              <th>Nom Propriétaire</th>
              <th>Prénom</th>
              <th>Adresse Bien</th>
              <th>Nom Locataire</th>
              <th>Prénom Locataire</th>
              <th>Total encaissé</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in proprietairesData.data" :key="item.id">
              <td>{{ item.proprio_nom }}</td>
              <td>{{ item.proprio_prenom }}</td>
              <td>{{ item.bien_adresse }}</td>
              <td>{{ item.locat_nom }}</td>
              <td>{{ item.locat_prenom }}</td>
              <td>{{ formatMontant(item.montant_total) }}</td>
            </tr>
          </tbody>
        </table>
        <div class="d-flex mt-4 justify-content-center">
            <pagination
                :data="paginationMetaProprio"
                :limit=5
                @pagination-change-page="fetchProprietaires"
            ></pagination>
        </div>
      </div>

    </div>

    <div v-if="activeTab === 'agence'">
       <h2 class="text-center headerRapport">Rapport Global de l'Agence</h2>

       <div class="d-flex align-items-start justify-content-between">
        <div class="flex-1 pr-5 pt-2">
          <!-- Filtres pour les dates -->
          <div class="d-flex justify-content-between align-items-end gap-15 mb-3">
            <div class="d-flex align-items-end gap-15">
                <div>
                  <label for="debut" class="mr-2">Date début :</label>
                  <date-picker v-model="debut" class="w-100 mr-4"  required valueType="YYYY-MM-DD" input-class="form-control w-100" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>
                </div>
                <div>
                  <label for="fin" class="mr-2">Date fin :</label>
                   <date-picker v-model="fin" class="w-100 mr-4"  required valueType="YYYY-MM-DD" input-class="form-control w-100" placeholder="dd/mm/yyyy" format="DD/MM/YYYY"></date-picker>

                </div>
                <button class="btn btn-primary" @click="fetchAgence">Filtrer</button>
            </div>
            <div v-if="debut!= '' && fin !=''">
              <!--button class="btn btn-md btn-success" @click="generationRapportLocataire"><i class="fa fa-eye"></i> Voir le Rapport</button-->
            </div>
          </div>
           <table class="table table-bordered mb-4 text-center">
              <thead>
                <tr>
                  <th>💰 Total encaissé</th>
                  <th>📊 Montant attendu</th>
                  <th>❌ Impayés</th>
                  <th>📈 Taux de recouvrement</th>
                  <th>📄 Baux actifs</th>
                  <th>👥 Locataires actifs</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><label class="badge badge-success">{{ formatMontant(metaAgence.total_paye) }}</label></td>
                  <td><label class="badge badge-warning">{{ formatMontant(metaAgence.total_attendu) }}</label></td>
                  <td><label class="badge badge-danger">{{ formatMontant(metaAgence.total_impayes) }}</label></td>
                  <td><label class="badge badge-primary">{{ metaAgence.taux_recouvrement }}%</label></td>
                  <td><label class="badge badge-info">{{ metaAgence.total_baux }}</label></td>
                  <td><label class="badge badge-info">{{ metaAgence.total_locataires }}</label></td>
                </tr>
              </tbody>
            </table>
        </div>

        <div class="mb-5">
          <Pie :data="pieChartDataAgence" :options="pieChartOptionsAgence" style="max-width: 400px; margin:auto" />
          </div>
       </div>

        <table class="table table-hover table-striped">
          <thead>
            <tr>
              <th>Bien</th>
              <th>Locataire</th>
              <th>Mois</th>
              <th>Loyer attendu</th>
              <th>Total payé</th>
              <th>Statut</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in agenceData" :key="item.paiement_id">
              <td>{{ item.bien_nom }} {{ item.bien_adresse }}</td>
              <td>{{ item.locat_nom }} {{ item.locat_prenom }}</td>
              <td>{{ item.paiement_mois_location }}</td>
              <td>{{ formatMontant(item.bail_montant_ht) }}</td>
              <td>{{ formatMontant(getTotalPaiement(item.paiement_recu)) }}</td>
              <td>
                <span :style="{ color: item.paiement_etat === 3 ? 'green' : item.paiement_etat === 2 ? 'orange' : 'red' }">
                  {{ getStatutPaiement(item.paiement_etat) }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="d-flex mt-4 justify-content-center">
            <pagination
                :data="paginationMetaAgence"
                :limit=10
                @pagination-change-page="fetchAgence"
            ></pagination>
        </div>
    </div>

    <modalFile v-if="locataireSelected!=null">
      <template #footer-action>
         <button @click="envoyerAuLocataire" class="btn btn-success">
          <i class="fa fa-paper-plane"></i> Envoyer le Rapport à {{ locataireSelected.locat_prenom }} {{ locataireSelected.locat_nom }}
        </button>
        <button @click="telechargerPdf" class="btn btn-danger">
           <i class="fa fa-download"></i> Télécharger le PDF
        </button>
      </template>
     </modalFile>

     <modalFile v-if="proprioSelected!=null">
      <template #footer-action>
         <button @click="envoyerAuProprio" class="btn btn-success">
          <i class="fa fa-paper-plane"></i> Envoyer le Rapport à {{ proprioSelected.proprio_prenom }} {{ proprioSelected.proprio_nom }}
        </button>
        <button @click="telechargerPdf" class="btn btn-danger">
           <i class="fa fa-download"></i> Télécharger le PDF
        </button>
      </template>
     </modalFile>
  </div>
</template>


<script>
import DatePicker from 'vue2-datepicker';
import { Bar, Pie } from 'vue-chartjs';
import { EventBus } from "../../event-bus";
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js';
import ProprietaireSelect from "../../components/utils/ProprietaireSelect.vue";
import modalFile from '../../components/modal/viewFile.vue';



ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

export default {
  name: "RapportReleveLoyer",
   components: {
    DatePicker,
    Bar,
    Pie,
    ProprietaireSelect,
    modalFile
  },
  props: ["listProprio", "listLocataire", "env"],
  computed: {
    totalGeneral2() {
      return 0;
      return this.locatairesData.reduce((total, item) => {
        return total + this.getTotalPaiement(item.paiement_recu);
      }, 0);
    }
  },
  watch: {
    deep: true,
       paginate: function(){
            this.fetchLocataires();
       },

    }
  ,
  data() {
    return {
      activeTab: 'locataires',
      locatairesData: {},
      proprietairesData: [],
      paginationMeta: {},
      paginationMetaAgence: {},
      paginationMetaProprio: {},
      totalLocataires: 0,
      totalLignes: 0,
      debut: '',
      fin: '',
      proprioSelected: null,
      locataireSelected: null,
      proprioID: '',
      locataireID: '',
      listProprioTab: [],
      pieChartData: null,
      pieChartOptions: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      },
      barChartData: {
        labels: [],
        datasets: []
      },
      barChartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          title: {
            display: true,
            text: 'Montant total encaissé par locataire'
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      },
      rapportGenerate: null,
      rapportGeneratePublicURL: null,
      paginate: 5,
      totalGeneral: 0,
      montantPaye: 0,
      montantImpayes: 0,
      // Agence
      agenceData: [],
      metaAgence: {},
      pieChartDataAgence: null,
      pieChartOptionsAgence: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom',
          },
        },
      },
    };
  },
  mounted() {
    this.listProprioTab=this.listProprio;
    this.listProprio.map(function (x){
          return x.item_data = x.proprio_nom + ' ' + x.proprio_prenom + ' (' +x.proprio_id +')';
        }); this.listProprio.map(function (x){
          return x.item_data = x.proprio_nom + ' ' + x.proprio_prenom + ' (' +x.proprio_id +')';
        });
    this.listLocataire.map(function (x){
          return x.item_data = x.locat_nom + ' ' + x.locat_prenom + ' (' +x.locat_id +')';
        });
    this.fetchLocataires();
    this.fetchProprietaires();
    this.fetchAgence();

  },
  methods: {
    fetchLocataires(page=1) {
      const params = {};

      if (this.debut) params.debut = this.debut;
      if (this.fin) params.fin = this.fin;
      if (this.locataireID) params.locataireID = this.locataireID;

      axios.get('/rapports/loyers/locataires?paginate='+this.paginate+'&page=' + page, { params }).then(res => {
        if (res.data) {
          this.locatairesData = res.data;
          this.paginationMeta = res.data.meta.pagination;
          this.totalGeneral = this.formatMontant(res.data.meta?.total_general || 0);
          this.totalLocataires = res.data.meta?.total_locataires || 0;
          this.totalLignes = res.data.meta?.total_lignes || 0;
          this.montantImpayes = res.data.meta?.total_impayes || 0;
          this.montantPaye =  res.data.meta?.total_general || 0;

          this.updatePieData();
        }
      });
    },
    updatePieData() {
     // this.montantPaye = this.totalGeneral;

      const labels = ['Montants Payés', 'Montants Impayés'];
      console.log(this.montantPaye,'---',this.montantImpayes)
      const data = [this.montantPaye, this.montantImpayes];
      const backgroundColors = ['#28a745', '#dc3545'];

      this.pieChartData = {
        labels,
        datasets: [
          {
            label: 'Répartition des paiements',
            backgroundColor: backgroundColors,
            data
          }
        ]
      };
    },
    getTotalPaiement(paiementRecu) {
      if (!paiementRecu) return 0;
      let total = 0;
      try {
        const paiements = typeof paiementRecu === 'string' ? JSON.parse(paiementRecu) : paiementRecu;
        total = paiements.reduce((acc, curr) => acc + parseFloat(curr.paiementMontant || 0), 0);
      } catch (e) {
        console.warn('Erreur parsing paiement_recu', e);
      }
      return total;
    },
    fetchProprietairesOld() {
      axios.get('/rapports/loyers/proprietaires').then(res => {
        if (res.data.code === 0) {
          this.proprietairesData = res.data.data;

        }
      });
    },
    fetchProprietaires(page=1) {
      const params = {};
      if (this.debut) params.debut = this.debut;
      if (this.fin) params.fin = this.fin;
      if (this.proprioID) params.proprioID = this.proprioID;

      axios.get('/rapports/loyers/proprietaires?paginate='+this.paginate+'&page=' + page, { params }).then(res => {
        if (res.data.code === 0) {
          this.proprietairesData = res.data;
          this.paginationMetaProprio = res.data.meta.pagination;
          // Mise à jour du graphique
          this.updateChart();
         //.barChartData.labels = res.data.data.map(item => `${item.locat_nom} ${item.locat_prenom}`);
         // this.barChartData.datasets[0].data = res.data.data.map(item => item.montant_total);
        }
      });
    },
     updateChart() {
      const labels = this.proprietairesData.data.map(item => `${item.locat_nom} ${item.locat_prenom}`);
      const data = this.proprietairesData.data.map(item => item.montant_total);
       const backgroundColors = labels.map(() => this.getRandomColor());

      this.barChartData = {
        labels,
        datasets: [
          {
            label: 'Montant encaissé (FCFA)',
            backgroundColor: backgroundColors,
            data
          }
        ]
      };
    },
    formatMontant(montant) {
      return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(montant);
    },
    generationEncaissementLoyer(){
      const params = {};
      if (this.debut) params.debut = this.debut;
      if (this.fin) params.fin = this.fin;
      if (this.proprioID) params.proprioID = this.proprioID;
      if (this.proprioSelected){
          params.proprietaire = this.proprioSelected.proprio_prenom+' '+this.proprioSelected.proprio_nom;
          params.proprietaire_indicatif = this.proprioSelected.proprio_indicatif_1;
          params.proprietaire_telephone = this.proprioSelected.proprio_tel_1;
          params.proprietaire_adresse = this.proprioSelected.proprio_adresse;
          params.proprietaire_ville = this.proprioSelected.proprio_ville;
          params.proprietaire_pays = this.proprioSelected.proprio_pays;

      }

      axios.get('/rapports/loyers/generation_encaissement_loyer', { params }).then(res => {
        if (res.data.code === 0) {
          console.log("Data >>", res)
          this.rapportGenerate = res.data.file_path;
          this.rapportGeneratePublicURL = res.data.public_path;
          this.showRapport(res.data.file_path);
        }else{
          Vue.swal.fire(
            res.data.message,
            '',
            'error'
          );
        }
      });
    },
    onLocataireChoisi(locat){
      this.locataireSelected = locat;
      this.locataireID = locat.locat_id;
      this.fetchLocataires();
    },
    onProprioChoisi(proprio){
      this.proprioSelected = proprio;
      this.proprioID = proprio.proprio_id;
      this.fetchProprietaires();
    },

    showRapport(urlFile){
        EventBus.$emit('VIEW_FILE', {
            pathFile: urlFile
        });
    },
    envoyerAuProprio(){
      const params = {};
      if (this.debut) params.debut = this.debut;
      if (this.fin) params.fin = this.fin;
      if (this.proprioID) params.proprioID = this.proprioID;
      if (this.proprioSelected){
          params.proprietaire = this.proprioSelected.proprio_prenom+' '+this.proprioSelected.proprio_nom;
          params.proprietaire_indicatif = this.proprioSelected.proprio_indicatif_1;
          params.proprietaire_telephone = this.proprioSelected.proprio_tel_1;
          params.proprietaire_adresse = this.proprioSelected.proprio_adresse;
          params.proprietaire_ville = this.proprioSelected.proprio_ville;
          params.proprietaire_pays = this.proprioSelected.proprio_pays;
          params.proprio_email = this.proprioSelected.proprio_email;
          params.rapportGenerate = this.rapportGeneratePublicURL ;


      }
      axios.get('/rapports/envoye_au_proprio', { params }).then(res => {
        if (res.data.code === 0) {
          Vue.swal.fire(
            'Rapport envoyé!',
            '',
            'success'
          );
        }else{
          Vue.swal.fire(
            res.data.message,
            '',
            'error'
          );
        }
      })
    },
    telechargerPdf() {
      const fileUrl = this.env+"/"+this.rapportGeneratePublicURL; // exemple : '/encaissement_loyer/2024/PROP-00000/fichier.pdf'
      console.log(">>>", fileUrl)
      const link = document.createElement('a');
      link.href = fileUrl;
      link.setAttribute('download', 'rapport-loyer.pdf'); // nom du fichier téléchargé
      link.setAttribute('target', '_blank');
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    },
    getStatutPaiement(etatPaiement) {
      var etat = '';
      switch(etatPaiement){
        case 3: etat='✅ Payé'; break;
        case 2: etat='⚠️ Partiel'; break;
        case 0: etat='❌ Impayé'; break;
        default: etat=etat.toString()+'inconnu';
      }

      return etat;
      /*
      const totalPaye = this.getTotalPaiement(item.paiement_recu);
      const montantLoyer = item.bail_montant_ht || 0;
      if (totalPaye >= montantLoyer) return '✅ Payé';
      if (totalPaye > 0 && totalPaye < montantLoyer) return '⚠️ Partiel';
      return '❌ Impayé';*/
    },
    onInputSelectLocataire(value) {
      if (!value) {
        this.locataireSelected = null;
        this.locataireID = '';
        this.fetchLocataires(); // 💡 appel de ton action de réinitialisation
      }
    },
    onInputSelectProprio(value) {
      if (!value) {
        this.proprioSelected = null;
        this.proprioID = '';
        this.fetchProprietaires(); // 💡 appel de ton action de réinitialisation
      }
    },
    generationRapportLocataire(){
      const params = {};
      if (this.debut) params.debut = this.debut;
      if (this.fin) params.fin = this.fin;
      if (this.locataireID) params.locataireID = this.locataireID;
      if (this.locataireSelected){
          params.locataire = this.locataireSelected.locat_prenom+' '+this.locataireSelected.locat_nom;
          params.locataire_indicatif = this.locataireSelected.locat_indicatif_1;
          params.locataire_telephone = this.locataireSelected.locat_tel_1;
          params.locataire_adresse = this.locataireSelected.locat_adresse;
          params.locataire_ville = this.locataireSelected.locat_ville;
          params.locataire_pays = this.locataireSelected.locat_pays;

      }

      axios.get('/rapports/loyers/generation_rapport_locataire', { params }).then(res => {
        if (res.data.code === 0) {
          console.log("Data >>", res)
          this.rapportGenerate = res.data.file_path;
          this.rapportGeneratePublicURL = res.data.public_path;
          this.showRapport(res.data.file_path);
        }else{
          Vue.swal.fire(
            res.data.message,
            '',
            'error'
          );
        }
      });
    },
    envoyerAuLocataire(){
      const params = {};
      if (this.debut) params.debut = this.debut;
      if (this.fin) params.fin = this.fin;
      if (this.locataireID) params.locataireID = this.locataireID;
      if (this.locataireSelected){
          params.locataire = this.locataireSelected.locat_prenom+' '+this.locataireSelected.locat_nom;
          params.locataire_indicatif = this.locataireSelected.locat_indicatif_1;
          params.locataire_telephone = this.locataireSelected.locat_tel_1;
          params.locataire_adresse = this.locataireSelected.locat_adresse;
          params.locataire_ville = this.locataireSelected.locat_ville;
          params.locataire_pays = this.locataireSelected.locat_pays;
          params.locataire_email = this.locataireSelected.locat_email;
          params.rapportGenerate = this.rapportGeneratePublicURL ;


      }
      axios.get('/rapports/envoye_au_locataire', { params }).then(res => {
        if (res.data.code === 0) {
          Vue.swal.fire(
            'Rapport envoyé!',
            '',
            'success'
          );
        }else{
          Vue.swal.fire(
            res.data.message,
            '',
            'error'
          );
        }
      })
    },
     // Agence
    fetchAgence(page=1) {
      const params = {};

      if (this.debut) params.debut = this.debut;
      if (this.fin) params.fin = this.fin;
      if (this.locataireID) params.locataireID = this.locataireID;

       axios.get('/rapports/loyers/agence?paginate='+this.paginate+'&page=' + page, {params}).then((res) => {
        if (res.data.code === 0) {
          this.agenceData = res.data.data;
          this.metaAgence = res.data.meta;
          this.paginationMetaAgence = res.data.meta.pagination;
          this.updateChartAgence();
        }
      });
    },
    updateChartAgence() {
      this.pieChartDataAgence = {
        labels: ['Payé', 'Impayé'],
        datasets: [
          {
            data: [this.metaAgence.total_paye, this.metaAgence.total_impayes],
            backgroundColor: ['#28a745', '#dc3545'],
          },
        ],
      };
    },
    getTotalPaiementAgence(paiementRecu) {
       try {
        const paiements = typeof paiementRecu === 'string' ? JSON.parse(paiementRecu) : paiementRecu;
        return paiements.reduce((acc, curr) => acc + parseFloat(curr.paiementMontant || 0), 0);
       } catch (e) {
        return 0;
      }
    }
  }
};
</script>

<style scoped>
.nav-link.active {
  font-weight: bold;
  color: #007bff;
}
.bar-chart {
  margin-bottom: 20px;
}
</style>
