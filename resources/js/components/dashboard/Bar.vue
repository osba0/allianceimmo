<template>
  <div>
    <Bar
      ref="myChart"
      id="my-chart-id"
      :options="chartOptions"
      :data="chartData"
      :height="100"
    />
  </div>
</template>

<script>
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
import axios from 'axios'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

export default {
  name: 'BarChart',
  components: { Bar },
  data() {
    return {
      chartData: {
        labels: [],
        datasets: [
          {
            label: 'Crédits',
            data: [],
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
          },
          {
            label: 'Débits',
            data: [],
            backgroundColor: 'rgba(255, 99, 132, 0.6)',
          },
          {
            label: 'Solde',
            data: [],
            backgroundColor: 'rgba(0, 123, 83, 0.6)',
          },
        ],
      },
      chartOptions: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Montant en F CFA',
            },
          },
        },
        plugins: {
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: 'Solde Mensuel des Crédits et Débits',
          },
        },
      },
    }
  },
  mounted() {
    this.fetchChartData();
  },
  methods: {
    async fetchChartData() {
      try {
        const response = await axios.get('/api/solde-mensuel');
        console.log('Données récupérées:', response.data);

        const months = response.data.months;
        const credits = response.data.credits;
        const debits = response.data.debits;
        const soldes = response.data.solde;


        // Mettre à jour chartData
        this.chartData = {
          labels: months,
          datasets: [
             {
              label: 'Solde',
              data: soldes,
              backgroundColor: 'rgba(0, 123, 83, 0.6)',
            },
            {
              label: 'Crédits',
              data: credits,
              backgroundColor: 'rgba(54, 162, 235, 0.6)',
            },
            {
              label: 'Débits',
              data: debits,
              backgroundColor: 'rgba(255, 99, 132, 0.6)',
            },

          ],
        };
      } catch (error) {
        console.error('Erreur lors de la récupération des données:', error);
      }
    },
  },
}
</script>

<style scoped>
/* Ajoute des styles si nécessaire */
</style>
