<template>
  <div class="badge-container">
    <!-- RECTO -->
    <div class="badgeImprimable badge-recto">
      <div class="accroche"></div>
      <div class="logoAgenceBadge">
          <img :src="logo" alt="Logo" style="vertical-align: middle;"/>
          <span class="ml-1" style="font-weight: bold; font-size: 18px; text-transform: uppercase;">Alliance Bazics Immo</span>
      </div>
      <img class="photo" :src="photo" alt="Photo" />

      <div class="infos-badge">
        <h3><strong>{{ proprietaire.nom }} {{ proprietaire.prenom }}</strong></h3>
        <p><strong>IDENTIFIANT :</strong> {{ proprietaire.id }}</p>
        <p><strong>Tél :</strong> {{ proprietaire.telephone }}</p>
        <p><strong>Email :</strong> {{ proprietaire.email }}</p>

      </div>
    </div>

    <!-- VERSO -->
    <div class="badgeImprimable badge-verso">
      <div class="accroche"></div>
      <p class="scan-text">📲 Scannez pour consulter les encaissements</p>
      <!--img class="qr" :src="qrCode" alt="QR Code" /-->
      <div class="contentQRCODE d-flex align-items-center justify-content-center"><qr-code :text="qrCode" :size="size"></qr-code>
     </div>
      <p class="text-center"><span>Pin: {{ proprietaire.pin }}</span></p>
      <p class="footer-badge">Alliance Immo | www.allianceimmo.net</p>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    photo: String,
    logo: String,
    qrCode: String,
    size: Number,
    proprietaire: Object
  }
}
</script>

<style scoped>
  .infos-badge h3{
    font-size: 22px;
  }
  .contentQRCODE{
    position: relative;
    z-index: 5;
  }
.badge-container {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 20px;
  flex-direction: column;
  align-items: center;
  position: relative;
}
.accroche{
  position: absolute;
  top: -8px;
  width: 10%;
  height: 16px;
  left: 45%;
  border-radius: 16px;
  background: #373737;
}
.badgeImprimable {
  width: 130mm;
  height: 70mm;
  border: 1px solid #ccc;
  border-radius: 6px;
  box-shadow: 0 0 5px #999;
  padding: 10px;
  box-sizing: border-box;
  background: white;
  position: relative;
  overflow: hidden;
}
.badgeImprimable::before{
  width: 100%;
  height: 300px;
  border-radius: 100%;
  background: #cdc291e5;
  position: absolute;
  bottom: -230px;
  right: -150px;
  z-index: 1;
  content: " ";

}
.badgeImprimable::after{
width: 100%;
  height: 300px;
  border-radius: 100%;
  background: #4173a2ad;
  position: absolute;
  bottom: -200px;
  left: -150px;
  z-index: 1;
  content: " ";

}
.badge-recto .logoAgenceBadge{
  position: absolute;
  top: 10px;
  left: 10px;
}

.badge-recto .logoAgenceBadge img{
  width: 80px;
  height: auto;
}

.badge-recto .photo {
  width: 120px;
  height: auto;
  object-fit: cover;
  position: absolute;
  top: 10px;
  right: 10px;
  border-radius: 4px;
  z-index: 12;
}

.infos-badge {
  margin-top: 90px;
  font-size: 14px;
  padding-left: 15px;
  position: relative;
  z-index: 25;
}

.infos h3 {
  margin: 0;
  font-size: 18px;
  color: #004c97;
}

.badge-verso {
  padding-top: 30px;
}

.badge-verso .qr {
  width: 260px;
  margin: 10px auto;
  display: block;
}

.scan-text {
  text-align: center;
  font-weight: bold;
  font-size: 13px;
}

.footer-badge {
  font-size: 10px;
  color: #000;
  text-align: center;
  position: absolute;
  bottom: 5px;
  width: 100%;
  z-index: 5;
  left: 0;
}
</style>
