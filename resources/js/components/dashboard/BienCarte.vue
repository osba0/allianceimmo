<template>
  <div style="height: 90%; width: 100%;">
    <div id="mapId" style="height: 100%; width: 100%;"></div>
  </div>
</template>

<script>
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

import customIconImageOrange from '/assets/images/pointeur-orange.png';
import customIconImageVerte from '/assets/images/pointeur-vert.png'

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
        const customIcon = L.icon({
          iconUrl: bien.has_local_disponible==0 ? customIconImageOrange:customIconImageVerte,
          iconSize: [25, 41],
          iconAnchor: [20, 40],
          popupAnchor: [0, -40]
        });

        let imagesHtml = '';

        try {
          const photos = JSON.parse(bien.bien_photos || '[]');
          photos.forEach(photo => {
            imagesHtml += `<img src="/assets/biens/${photo}" style="margin-bottom: 2px;" />`;
          });
        } catch (e) {
          console.warn("Erreur de parsing des photos pour le bien", bien.bien_id, e);
        }

        const popupContent = `
          <div>
            ${imagesHtml}
            Propriétaire: <strong><u>${bien.proprio_nom} ${bien.proprio_prenom}</u></strong><br/>
            Nom du bien: <strong>${bien.bien_nom || ''}</strong><br/>
            Adresse: ${bien.bien_adresse || ''}<br/>
            Ville : ${bien.bien_ville || ''}
          </div>
        `;
        const marker = L.marker(position, { icon: customIcon }).addTo(this.map);




        marker.bindPopup(popupContent);

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
