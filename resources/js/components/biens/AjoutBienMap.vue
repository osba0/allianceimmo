<template>
  <div style="height: 100%; width: 100%;">
    <div class="d-flex align-items-center gap-15 px-3 my-3">
      <input v-model="adresse" @change="geocoderAdresse" placeholder="Entrer l'adresse bien" class="form-control mb-0" />
      <button class="btn btn-sm btn-primary" @click="geocoderAdresse">Rechercher</button>
      <button class="btn btn-sm btn-success text-nowrap" :disabled="!addressFind" @click="enregistrerEtFermer">
        Enregistrer la position
      </button>
    </div>

    <div id="mapId2" style="height: 100%; width: 100%;"></div>
  </div>
</template>

<script>
import L from 'leaflet';

export default {
  props: {
    adresseInput: String,
    lat: Number,
    lng: Number
  },
  data() {
    return {
      map: null,
      marker: null,
      adresse: this.adresseInput || '',
      coordonnees: {
        lat: this.lat || 14.6928,
        lng: this.lng || -17.4467
      },
      addressFind: false
    };
  },
  watch: {
    adresseInput(newVal) {
      this.adresse = newVal;
    }
  },
  mounted() {
    this.map = L.map('mapId2').setView([this.coordonnees.lat, this.coordonnees.lng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors',
    }).addTo(this.map);

    if (this.lat && this.lng) {
      console.log('>>> lat', this.coordonnees.lat);
      console.log('>>> lng', this.coordonnees.lng)
      this.addOrUpdateMarker(this.coordonnees.lat, this.coordonnees.lng);
    } else if (this.adresse) {
      this.geocoderAdresse();
    }

    this.map.on('click', (e) => {
      const { lat, lng } = e.latlng;
      this.addOrUpdateMarker(lat, lng);
      this.coordonnees = { lat, lng };
      this.$emit('update-coordonnees', this.coordonnees);
    });
  },
  methods: {
    geocoderAdresse() {
      if (!this.adresse) return;

      const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.adresse)}`;

      axios.get(url).then(res => {
        if (res.data.length > 0) {
          const result = res.data[0];
          const lat = parseFloat(result.lat);
          const lng = parseFloat(result.lon);

          this.addOrUpdateMarker(lat, lng);
          this.map.setView([lat, lng], 16);
          this.coordonnees = { lat, lng };
          this.addressFind = true;
          this.$emit('update-coordonnees', this.coordonnees);
        } else {

          Vue.swal.fire(
                       '📍 Adresse introuvable',
                        'Veuillez cliquer sur la carte pour définir la position manuellement.',
                        'warning'
                    );
        }
      });
    },
    addOrUpdateMarker(lat, lng) {
      this.addressFind = true;

      // ✅ Supprimer l'ancien marqueur s'il existe
      if (this.marker) {
        this.map.removeLayer(this.marker);
      }

      // ✅ Ajouter un nouveau marqueur à la nouvelle position
      this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);

      // ✅ Récupérer la nouvelle position si on le déplace
      this.marker.on('dragend', (e) => {
        const pos = e.target.getLatLng();
        this.coordonnees = { lat: pos.lat, lng: pos.lng };
        this.$emit('update-coordonnees', this.coordonnees);
      });

      // ✅ Mettre à jour les coordonnées dans le composant
      this.coordonnees = { lat, lng };
      this.$emit('update-coordonnees', this.coordonnees);
    },
    enregistrerEtFermer() {
      this.$emit('save-position', this.coordonnees); // envoyer au parent
      this.$emit('close-map'); // déclenche un événement que le parent écoutera pour fermer
       Vue.swal.fire(
                '📍 Position enregistrée',
                'La position a été enregistrée avec succès.',
                'success'
                    );
    }

  }
};
</script>
