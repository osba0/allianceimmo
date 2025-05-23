<template>
  <div style="height: 90%; width: 100%;">
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
  data() {
    return {
      map: null,
      biens: [],
    };
  },
 mounted() {
  this.map = L.map('mapId').setView([14.6928, -17.4467], 12);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
  }).addTo(this.map);

  axios.get('/bien/with-location').then((res) => {
    this.biens = res.data;

    const bounds = L.latLngBounds(); // 👉 initialise les limites

    this.biens.forEach((bien) => {
      if (bien.latitude && bien.longitude) {
        const position = [bien.latitude, bien.longitude];
        const marker = L.marker(position).addTo(this.map);

        marker.bindPopup(`
          <strong>${bien.bien_nom || 'Bien'}</strong><br/>
          ${bien.bien_adresse || ''}<br/>
          Ville : ${bien.bien_ville || ''}
        `);

        bounds.extend(position); // 👉 ajoute la position aux limites
      }
    });

    if (this.biens.length > 0) {
      this.map.fitBounds(bounds); // 👉 ajuste la vue à tous les marqueurs
    }
  });
},
};
</script>
