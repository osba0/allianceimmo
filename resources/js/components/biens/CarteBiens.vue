<template>
  <div style="height: 100%; width: 100%;">
    <div id="mapId" style="height: 100%; width: 100%;"></div>
  </div>
</template>

<script>
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({
  iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
  iconUrl: require('leaflet/dist/images/marker-icon.png'),
  shadowUrl: require('leaflet/dist/images/marker-shadow.png'),
});

export default {
  props: {
    showCarte: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      map: null,
      biens: [],
       mapInitialized: false
    };
  },
  watch: {
    showCarte(newVal) {
      if (newVal) {
        this.$nextTick(() => {
          this.initMap(); // Recharge à chaque ouverture du modal
        });
      }
    }
  },
  methods: {
    initMap() {
      if (!this.map) {
        this.map = L.map('mapId', {
          center: [14.6928, -17.4467],
          zoom: 15,
          maxZoom: 17, // 🔒 Zoom maximal autorisé (plus grand = plus proche)
          minZoom: 10  // 🔓 Optionnel : zoom minimum
        });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; OpenStreetMap contributors'
        }).addTo(this.map);
      }

      // Supprime les anciens marqueurs
      this.map.eachLayer((layer) => {
        if (layer instanceof L.Marker) {
          this.map.removeLayer(layer);
        }
      });

      axios.get('/bien/with-location').then((res) => {
          this.biens = res.data;
          const markers = [];

          this.biens.forEach((bien) => {
            if (bien.latitude && bien.longitude) {
              const lat = parseFloat(bien.latitude);
              const lng = parseFloat(bien.longitude);

              const marker = L.marker([lat, lng]).addTo(this.map);
              marker.bindPopup(`
                <strong>${bien.bien_nom || 'Bien'}</strong><br/>
                ${bien.bien_adresse || ''}<br/>
                Ville : ${bien.bien_ville || ''}
              `);
              markers.push([lat, lng]);
            }
          });

          // 👇 Appliquer un fitBounds avec zoom maximal défini
          if (markers.length > 0) {
            const bounds = L.latLngBounds(markers);
            this.map.fitBounds(bounds, { maxZoom: 16 }); // Limite le zoom lors du centrage
          }

          this.map.invalidateSize(); // important pour les modals
        });
    }
  }

};
</script>
