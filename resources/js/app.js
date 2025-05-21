
// Views
import Navbar from "./components/global/Navbar";
import Footer from "./components/global/Footer";
import Sidebar from "./components/global/Sidebar";
import Container from "./components/Container";
import SoldeTopBarre from "./components/global/SoldeTopBarre"; 
import Login from "./components/auth/Login";
import Register from "./components/auth/Register";
import PasswordReset from "./components/auth/PasswordReset";
import PasswordUpdate from "./components/auth/PasswordUpdate";
import Proprio from "./components/proprio/Index";
import Locataires from "./components/locataires/Index";
import BienImmeuble from "./components/biens/Index";
import Mandat from "./components/mandat_gerance/Index";
import Bail from "./components/bail/Index"; 
import Operations from "./components/operations/Index";  
import Charges from "./components/operations/Charges";  
import OperationsList from "./components/operations/List";  
import Pie from "./components/dashboard/Pie";
import Bar from "./components/dashboard/Bar";
import RoleManager from './components/config/RoleManager.vue';
import PermissionManager from './components/config/PermissionManager.vue';
import RolePermissionAssigner from './components/config/RolePermissionAssigner.vue';
import UserManager from './components/users/UserManager.vue';
import AgenceList from './components/agence/Index';
import RapportLoyers from './components/rapports/RapportLoyers';
import RapportEtReleveLoyer from './components/rapports/RapportEtReleveLoyer';
import NotificationDropdown from './components/notification/NotificationDropdown.vue';
import Notifications from './components/notification/Notifications.vue';
import VersementProprio from "./components/operations/VersementProprio";
import TachesAutomisees from "./components/taches/Index";

import moment from 'moment';

//import QuittanceLoyerTemplate from "./components/template/QuittanceLoyerTemplate";
//import TemplateDataTable from "./components/template/TemplateDataTable";
import PaiementLoyers from './components/paiementLoyer/Index';
import VueQRCodeComponent from 'vue-qrcode-component';


require("./bootstrap");

window.Vue = require("vue").default;

Vue.prototype.$userPermissions = window.userPermissions;
Vue.prototype.$userRoles = window.userRoles || [];

import Vue from "vue";
import { createApp } from "vue";
import Vuelidate from 'vuelidate';

Vue.component('pagination', require('laravel-vue-pagination')); 
Vue.use(Vuelidate);

import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
Vue.use(VueSweetalert2);

import VueCountryDropdown from "vue-country-dropdown";
Vue.use(VueCountryDropdown);

import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
Vue.component("v-select", vSelect);

Vue.filter('formatDateFR', function(value) {
    if (value) {
        return moment(String(value)).format('DD/MM/YYYY') // MM/DD/YYYY hh:mm'
    }
});

Vue.component('qr-code', VueQRCodeComponent);
require('./summernoteEditor');
Vue.mixin({
  methods: {
    supprimer_espace_mnt(value){
        if (value) {
            return value.replaceAll(" ", "");  
        }
    },
    format_date_FR(dte){
         if (dte) {
           return moment(String(dte)).format('DD/MM/YYYY')
          }
      },
    getRandomColor() {
      const letters = '0123456789ABCDEF';
      let color = '#';
      for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
      }
      return color;
    },
    currentDateTime() {
        const current = new Date();
        const date = current.getDate()+'/'+(current.getMonth()+1)+'/'+current.getFullYear();
        const time = current.getHours() + ":" + current.getMinutes() + ":" + current.getSeconds();
        const dateTime = date;

        return dateTime;
    },
    disabledFutureDate(date) {
              const today = new Date();
              today.setHours(0, 0, 0, 0);

              return date > today;
            },
    capitalizeFirstLetter: str => str.charAt(0).toUpperCase() + str.slice(1),
    helper_separator_amount: value => value.toString().replaceAll(' ', '').toString()
                    .replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1 "),
    unite( nombre ){
        var unite;
        switch( nombre ){
            case 0: unite = "zéro";     break;
            case 1: unite = "un";       break;
            case 2: unite = "deux";     break;
            case 3: unite = "trois";    break;
            case 4: unite = "quatre";   break;
            case 5: unite = "cinq";     break;
            case 6: unite = "six";      break;
            case 7: unite = "sept";     break;
            case 8: unite = "huit";     break;
            case 9: unite = "neuf";     break;
        }//fin switch
        return unite;
    },
    dizaine( nombre ){
        var dizaine;
        switch( nombre ){
            case 10: dizaine = "dix"; break;
            case 11: dizaine = "onze"; break;
            case 12: dizaine = "douze"; break;
            case 13: dizaine = "treize"; break;
            case 14: dizaine = "quatorze"; break;
            case 15: dizaine = "quinze"; break;
            case 16: dizaine = "seize"; break;
            case 17: dizaine = "dix-sept"; break;
            case 18: dizaine = "dix-huit"; break;
            case 19: dizaine = "dix-neuf"; break;
            case 20: dizaine = "vingt"; break;
            case 30: dizaine = "trente"; break;
            case 40: dizaine = "quarante"; break;
            case 50: dizaine = "cinquante"; break;
            case 60: dizaine = "soixante"; break;
            case 70: dizaine = "soixante-dix"; break;
            case 80: dizaine = "quatre-vingt"; break;
            case 90: dizaine = "quatre-vingt-dix"; break;
        }//fin switch
        return dizaine;
    },
    abs(a){
        if(a < 0) a=-a;
        return a; 
    },
    allCountriesISO2(){
        return [
            'AF', 'AX', 'AL', 'DZ', 'AS', 'AD', 'AO', 'AI', 'AQ', 'AG',
            'AR', 'AM', 'AW', 'AU', 'AT', 'AZ', 'BS', 'BH', 'BD', 'BB',
            'BY', 'BE', 'BZ', 'BJ', 'BM', 'BT', 'BO', 'BQ', 'BA', 'BW',
            'BV', 'BR', 'IO', 'BN', 'BG', 'BF', 'BI', 'CV', 'KH', 'CM',
            'CA', 'KY', 'CF', 'TD', 'CL', 'CN', 'CX', 'CC', 'CO', 'KM',
            'CG', 'CD', 'CK', 'CR', 'CI', 'HR', 'CU', 'CW', 'CY', 'CZ',
            'DK', 'DJ', 'DM', 'DO', 'EC', 'EG', 'SV', 'GQ', 'ER', 'EE',
            'SZ', 'ET', 'FK', 'FO', 'FJ', 'FI', 'FR', 'GF', 'PF', 'TF',
            'GA', 'GM', 'GE', 'DE', 'GH', 'GI', 'GR', 'GL', 'GD', 'GP',
            'GU', 'GT', 'GG', 'GN', 'GW', 'GY', 'HT', 'HM', 'VA', 'HN',
            'HK', 'HU', 'IS', 'IN', 'ID', 'IR', 'IQ', 'IE', 'IM', 'IL',
            'IT', 'JM', 'JP', 'JE', 'JO', 'KZ', 'KE', 'KI', 'KP', 'KR',
            'KW', 'KG', 'LA', 'LV', 'LB', 'LS', 'LR', 'LY', 'LI', 'LT',
            'LU', 'MO', 'MG', 'MW', 'MY', 'MV', 'ML', 'MT', 'MH', 'MQ',
            'MR', 'MU', 'YT', 'MX', 'FM', 'MD', 'MC', 'MN', 'ME', 'MS',
            'MA', 'MZ', 'MM', 'NA', 'NR', 'NP', 'NL', 'NC', 'NZ', 'NI',
            'NE', 'NG', 'NU', 'NF', 'MK', 'MP', 'NO', 'OM', 'PK', 'PW',
            'PS', 'PA', 'PG', 'PY', 'PE', 'PH', 'PN', 'PL', 'PT', 'PR',
            'QA', 'RE', 'RO', 'RU', 'RW', 'BL', 'SH', 'KN', 'LC', 'MF',
            'PM', 'VC', 'WS', 'SM', 'ST', 'SA', 'SN', 'RS', 'SC', 'SL',
            'SG', 'SX', 'SK', 'SI', 'SB', 'SO', 'ZA', 'GS', 'SS', 'ES',
            'LK', 'SD', 'SR', 'SJ', 'SE', 'CH', 'SY', 'TW', 'TJ', 'TZ',
            'TH', 'TL', 'TG', 'TK', 'TO', 'TT', 'TN', 'TR', 'TM', 'TC',
            'TV', 'UG', 'UA', 'AE', 'GB', 'US', 'UM', 'UY', 'UZ', 'VU',
            'VE', 'VN', 'VG', 'VI', 'WF', 'EH', 'YE', 'ZM', 'ZW',
        ];
    },
    countriesAuthorizedISO2(){
        return [
            'SN', 'FR'
        ];
    },
    getRegexForCountryTelMobile(country) {
      // Ajoutez des regex pour d'autres pays selon les besoins
      const regexMap = {
        '33': /^[0-9]{10}$/, // Exemple: France avec 10 chiffres uniquement
        '221': /^[0-9]{9}$/
      };

      return regexMap[country];
    },
    getRegexForCountryTelFixe(country) {
      // Ajoutez des regex pour d'autres pays selon les besoins
      const regexMap = {
        '33': /^[0-9]{10}$/, // Exemple: France avec 10 chiffres uniquement
        '221': /^[0-9]{9}$/
      };

      return regexMap[country];
    },
    getRegexEmail(){
        return /^[\w\.-]+@[a-zA-Z\d\.-]+\.[a-zA-Z]{2,}$/;
    },
    errorMessageTelephone(){
        return 'Téléphone incorrect';
    },
    errorMessageEmail(){
        return 'Email incorrect';
    },
    errorMessageEtage(){
        return "Nombre d'étage incorrect";
    },
    errorMessagePiece(){
        return "Nombre piéce incorrect";
    },
     errorMessageDuree(){
        return "Durée incorrect";
    },
    hasPermission(permission) {
        // Vérifier si le profil est 'root' (insensible à la casse)
        if (this.$userRoles.map(role => role.toLowerCase()).includes('root')) {
            return true;
        }
        return this.$userPermissions.includes(permission);
    },
    getNationale(){
        return [
        "Afghane",
        "Albanaise",
        "Algérienne",
        "Allemande",
        "Andorrane",
        "Angolaise",
        "Antiguaise-et-Barbudienne",
        "Argentine",
        "Arménienne",
        "Australienne",
        "Autrichienne",
        "Azerbaïdjanaise",
        "Bahamienne",
        "Bahreinienne",
        "Bangladaise",
        "Barbadienne",
        "Belge",
        "Belizienne",
        "Béninoise",
        "Bhoutanaise",
        "Biélorusse",
        "Birmane",
        "Bissau-Guinéenne",
        "Bolivienne",
        "Bosnienne",
        "Botswanaise",
        "Brésilienne",
        "Britannique",
        "Brunéienne",
        "Bulgare",
        "Burkinabée",
        "Burundaise",
        "Cambodgienne",
        "Camerounaise",
        "Canadienne",
        "Cap-verdienne",
        "Centrafricaine",
        "Chilienne",
        "Chinoise",
        "Chypriote",
        "Colombienne",
        "Comorienne",
        "Congolaise (Congo-Brazzaville)",
        "Congolaise (RDC)",
        "Cookienne",
        "Costaricienne",
        "Croate",
        "Cubaine",
        "Danoise",
        "Djiboutienne",
        "Dominicaine",
        "Dominiquaise",
        "Égyptienne",
        "Émirienne",
        "Équato-guinéenne",
        "Équatorienne",
        "Érythréenne",
        "Espagnole",
        "Estonienne",
        "Éthiopienne",
        "Fidjienne",
        "Finlandaise",
        "Française",
        "Gabonaise",
        "Gambienne",
        "Géorgienne",
        "Ghanéenne",
        "Grenadienne",
        "Guatémaltèque",
        "Guinéenne",
        "Guinéenne équatoriale",
        "Guyanienne",
        "Haïtienne",
        "Hellénique",
        "Hondurienne",
        "Hongroise",
        "Indienne",
        "Indonésienne",
        "Irakienne",
        "Iranienne",
        "Irlandaise",
        "Islandaise",
        "Israélienne",
        "Italienne",
        "Ivoirienne",
        "Jamaïcaine",
        "Japonaise",
        "Jordanienne",
        "Kazakhstanaise",
        "Kenyane",
        "Kirghize",
        "Kiribatienne",
        "Kittitienne-et-Névicienne",
        "Koweïtienne",
        "Laotienne",
        "Lesothane",
        "Lettone",
        "Libanaise",
        "Libérienne",
        "Libyenne",
        "Liechtensteinoise",
        "Lituanienne",
        "Luxembourgeoise",
        "Macédonienne",
        "Malaisienne",
        "Malawienne",
        "Maldivienne",
        "Malienne",
        "Maltaise",
        "Marocaine",
        "Marshallaise",
        "Mauricienne",
        "Mauritanienne",
        "Mexicaine",
        "Micronésienne",
        "Moldave",
        "Monegasque",
        "Mongole",
        "Monténégrine",
        "Mozambicaine",
        "Namibienne",
        "Nauruane",
        "Népalaise",
        "Nicaraguayenne",
        "Nigeriane",
        "Nigérienne",
        "Norvégienne",
        "Néo-Zélandaise",
        "Omanaise",
        "Ougandaise",
        "Ouzbèke",
        "Pakistanaise",
        "Palaosienne",
        "Palestinienne",
        "Panaméenne",
        "Papouane-Néo-Guinéenne",
        "Paraguayenne",
        "Néerlandaise",
        "Péruvienne",
        "Philippine",
        "Polonaise",
        "Portugaise",
        "Qatarienne",
        "Roumaine",
        "Russe",
        "Rwandaise",
        "Saint-Lucienne",
        "Saint-Marinaise",
        "Saint-Vincentaise-et-Grenadine",
        "Salomonaise",
        "Salvadorienne",
        "Samoane",
        "Santoméenne",
        "Saoudienne",
        "Sénégalaise",
        "Serbe",
        "Seychelloise",
        "Sierra-Léonaise",
        "Singapourienne",
        "Slovaque",
        "Slovène",
        "Somalienne",
        "Soudanaise",
        "Sri-Lankaise",
        "Sud-Africaine",
        "Sud-Soudanaise",
        "Suédoise",
        "Suisse",
        "Surinamaise",
        "Swazie",
        "Syrienne",
        "Tadjike",
        "Tanzanienne",
        "Tchadienne",
        "Tchèque",
        "Thaïlandaise",
        "Togolaise",
        "Tonguienne",
        "Trinidadienne",
        "Tunisienne",
        "Turkmène",
        "Turque",
        "Tuvaluane",
        "Ukrainienne",
        "Uruguayenne",
        "Vanuatuane",
        "Vénézuélienne",
        "Vietnamienne",
        "Yéménite",
        "Zambienne",
        "Zimbabwéenne"
      ];
    },
    numberToLetter( nombre ){
        var i, j, n, quotient, reste, nb ;
        var ch
        var numberToLetter='';
        //__________________________________
        
        if(  nombre.toString().replace( / /gi, "" ).length > 15  )  return "dépassement de capacité";
        if(  isNaN(nombre.toString().replace( / /gi, "" ))  )       return "Nombre non valide";

        nb = parseFloat(nombre.toString().replace( / /gi, "" ));
        if(  Math.ceil(nb) != nb  ) return  "Nombre avec virgule non géré.";
        
        n = nb.toString().length;
        switch( n ){
             case 1: numberToLetter = this.unite(nb); break;
             case 2: if(  nb > 19  ){
                           quotient = Math.floor(nb / 10);
                           reste = nb % 10;
                           if(  nb < 71 || (nb > 79 && nb < 91)  ){
                                 if(  reste == 0  ) numberToLetter = this.dizaine(quotient * 10);
                                 if(  reste == 1  ) numberToLetter = this.dizaine(quotient * 10) + "-et-" + this.unite(reste);
                                 if(  reste > 1   ) numberToLetter = this.dizaine(quotient * 10) + "-" + this.unite(reste);
                           }else numberToLetter = this.dizaine((quotient - 1) * 10) + "-" + this.dizaine(10 + reste);
                     }else numberToLetter = this.dizaine(nb);
                     break;
             case 3: quotient = Math.floor(nb / 100);
                     reste = nb % 100;
                     if(  quotient == 1 && reste == 0   ) numberToLetter = "cent";
                     if(  quotient == 1 && reste != 0   ) numberToLetter = "cent" + " " + this.numberToLetter(reste);
                     if(  quotient > 1 && reste == 0    ) numberToLetter = this.unite(quotient) + " cents";
                     if(  quotient > 1 && reste != 0    ) numberToLetter = this.unite(quotient) + " cent " + this.numberToLetter(reste);
                     break;
             case 4 :  quotient = Math.floor(nb / 1000);
                          reste = nb - quotient * 1000;
                          if(  quotient == 1 && reste == 0   ) numberToLetter = "mille";
                          if(  quotient == 1 && reste != 0   ) numberToLetter = "mille" + " " + this.numberToLetter(reste);
                          if(  quotient > 1 && reste == 0    ) numberToLetter = this.numberToLetter(quotient) + " mille";
                          if(  quotient > 1 && reste != 0    ) numberToLetter = this.numberToLetter(quotient) + " mille " + this.numberToLetter(reste);
                          break;
             case 5 :  quotient = Math.floor(nb / 1000);
                          reste = nb - quotient * 1000;
                          if(  quotient == 1 && reste == 0   ) numberToLetter = "mille";
                          if(  quotient == 1 && reste != 0   ) numberToLetter = "mille" + " " + this.numberToLetter(reste);
                          if(  quotient > 1 && reste == 0    ) numberToLetter = this.numberToLetter(quotient) + " mille";
                          if(  quotient > 1 && reste != 0    ) numberToLetter = this.numberToLetter(quotient) + " mille " + this.numberToLetter(reste);
                          break;
             case 6 :  quotient = Math.floor(nb / 1000);
                          reste = nb - quotient * 1000;
                          if(  quotient == 1 && reste == 0   ) numberToLetter = "mille";
                          if(  quotient == 1 && reste != 0   ) numberToLetter = "mille" + " " + this.numberToLetter(reste);
                          if(  quotient > 1 && reste == 0    ) numberToLetter = this.numberToLetter(quotient) + " mille";
                          if(  quotient > 1 && reste != 0    ) numberToLetter = this.numberToLetter(quotient) + " mille " + this.numberToLetter(reste);
                          break;
             case 7: quotient = Math.floor(nb / 1000000);
                          reste = nb % 1000000;
                          if(  quotient == 1 && reste == 0  ) numberToLetter = "un million";
                          if(  quotient == 1 && reste != 0  ) numberToLetter = "un million" + " " + this.numberToLetter(reste);
                          if(  quotient > 1 && reste == 0   ) numberToLetter = this.numberToLetter(quotient) + " millions";
                          if(  quotient > 1 && reste != 0   ) numberToLetter = this.numberToLetter(quotient) + " millions " + this.numberToLetter(reste);
                          break;  
             case 8: quotient = Math.floor(nb / 1000000);
                          reste = nb % 1000000;
                          if(  quotient == 1 && reste == 0  ) numberToLetter = "un million";
                          if(  quotient == 1 && reste != 0  ) numberToLetter = "un million" + " " + this.numberToLetter(reste);
                          if(  quotient > 1 && reste == 0   ) numberToLetter = this.numberToLetter(quotient) + " millions";
                          if(  quotient > 1 && reste != 0   ) numberToLetter = this.numberToLetter(quotient) + " millions " + this.numberToLetter(reste);
                          break;  
             case 9: quotient = Math.floor(nb / 1000000);
                          reste = nb % 1000000;
                          if(  quotient == 1 && reste == 0  ) numberToLetter = "un million";
                          if(  quotient == 1 && reste != 0  ) numberToLetter = "un million" + " " + this.numberToLetter(reste);
                          if(  quotient > 1 && reste == 0   ) numberToLetter = this.numberToLetter(quotient) + " millions";
                          if(  quotient > 1 && reste != 0   ) numberToLetter = this.numberToLetter(quotient) + " millions " + this.numberToLetter(reste);
                          break;  
             case 10: quotient = Math.floor(nb / 1000000000);
                            reste = nb - quotient * 1000000000;
                            if(  quotient == 1 && reste == 0  ) numberToLetter = "un milliard";
                            if(  quotient == 1 && reste != 0  ) numberToLetter = "un milliard" + " " + this.numberToLetter(reste);
                            if(  quotient > 1 && reste == 0   ) numberToLetter = this.numberToLetter(quotient) + " milliards";
                            if(  quotient > 1 && reste != 0   ) numberToLetter = this.numberToLetter(quotient) + " milliards " + this.numberToLetter(reste);
                            break;  
             case 11: quotient = Math.floor(nb / 1000000000);
                            reste = nb - quotient * 1000000000;
                            if(  quotient == 1 && reste == 0  ) numberToLetter = "un milliard";
                            if(  quotient == 1 && reste != 0  ) numberToLetter = "un milliard" + " " + this.numberToLetter(reste);
                            if(  quotient > 1 && reste == 0   ) numberToLetter = this.numberToLetter(quotient) + " milliards";
                            if(  quotient > 1 && reste != 0   ) numberToLetter = this.numberToLetter(quotient) + " milliards " + this.numberToLetter(reste);
                            break;  
             case 12: quotient = Math.floor(nb / 1000000000);
                            reste = nb - quotient * 1000000000;
                            if(  quotient == 1 && reste == 0  ) numberToLetter = "un milliard";
                            if(  quotient == 1 && reste != 0  ) numberToLetter = "un milliard" + " " + this.numberToLetter(reste);
                            if(  quotient > 1 && reste == 0   ) numberToLetter = this.numberToLetter(quotient) + " milliards";
                            if(  quotient > 1 && reste != 0   ) numberToLetter = this.numberToLetter(quotient) + " milliards " + this.numberToLetter(reste);
                            break;  
             case 13: quotient = Math.floor(nb / 1000000000000);
                            reste = nb - quotient * 1000000000000;
                            if(  quotient == 1 && reste == 0  ) numberToLetter = "un billion";
                            if(  quotient == 1 && reste != 0  ) numberToLetter = "un billion" + " " + this.numberToLetter(reste);
                            if(  quotient > 1 && reste == 0   ) numberToLetter = this.numberToLetter(quotient) + " billions";
                            if(  quotient > 1 && reste != 0   ) numberToLetter = this.numberToLetter(quotient) + " billions " + this.numberToLetter(reste);
                            break;  
             case 14: quotient = Math.floor(nb / 1000000000000);
                            reste = nb - quotient * 1000000000000;
                            if(  quotient == 1 && reste == 0  ) numberToLetter = "un billion";
                            if(  quotient == 1 && reste != 0  ) numberToLetter = "un billion" + " " + this.numberToLetter(reste);
                            if(  quotient > 1 && reste == 0   ) numberToLetter = this.numberToLetter(quotient) + " billions";
                            if(  quotient > 1 && reste != 0   ) numberToLetter = this.numberToLetter(quotient) + " billions " + this.numberToLetter(reste);numberToLetter
                            break;  
             case 15: quotient = Math.floor(nb / 1000000000000);
                            reste = nb - quotient * 1000000000000;
                            if(  quotient == 1 && reste == 0  ) numberToLetter = "un billion";
                            if(  quotient == 1 && reste != 0  ) numberToLetter = "un billion" + " " + this.numberToLetter(reste);
                            if(  quotient > 1 && reste == 0   ) numberToLetter = this.numberToLetter(quotient) + " billions";
                            if(  quotient > 1 && reste != 0   ) numberToLetter = this.numberToLetter(quotient) + " billions " + this.numberToLetter(reste);
                            break;  
         }//fin switch
         /*respect de l'accord de quatre-vingt*/
         if(  numberToLetter.substr(numberToLetter.length-"quatre-vingt".length,"quatre-vingt".length) == "quatre-vingt"  ) numberToLetter = numberToLetter + "s";
         
         return numberToLetter;
    },
   getMonthsBetweenDates(startDate, endDate) {
      const start = new Date(startDate+ "T23:00:00Z");
      const end = new Date(endDate+ "T23:00:00Z");

      if (isNaN(start) || isNaN(end)) {
        console.error("Les dates fournies ne sont pas valides.");
        return [];
      }

      const months = [];
      let currentYear = start.getFullYear();
      let currentMonth = start.getMonth(); // Mois (0 = Janvier)
      console.log("Start", start)

      console.log("started", currentMonth)

      // Ajouter le premier mois (startDate) dans le tableau
      months.push(`${(currentMonth + 1).toString().padStart(2, "0")}-${currentYear}`);

      // Passer au mois suivant
      currentMonth++;

      // Si on est à décembre, passer à l'année suivante
      if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
      }

      // Boucle pour ajouter les mois suivants
      while (currentYear < end.getFullYear() || (currentYear === end.getFullYear() && currentMonth <= end.getMonth())) {
        months.push(`${(currentMonth + 1).toString().padStart(2, "0")}-${currentYear}`);

        currentMonth++;
        if (currentMonth > 11) {
          currentMonth = 0;
          currentYear++;
        }
      }

      return months;
    }

  }
})


const app = new Vue({
    el: "#app",
    components: {
        Container,
        Navbar,
        Sidebar,
        SoldeTopBarre,
        Footer,
        Login,
        Register,
        PasswordReset,
        PasswordUpdate, 
        Proprio,
        VueCountryDropdown,
        Locataires,
        BienImmeuble,
        Mandat,
        Bail,
        Operations,
        Charges,
        OperationsList,
        Pie,
        RoleManager,
        PermissionManager,
        RolePermissionAssigner,
        UserManager,
        AgenceList,
        Bar,
       // TemplateDataTable,
       // QuittanceLoyerTemplate,
        PaiementLoyers,
        RapportLoyers,
        RapportEtReleveLoyer,
        NotificationDropdown,
        Notifications,
        VersementProprio,
        TachesAutomisees
    }
});
