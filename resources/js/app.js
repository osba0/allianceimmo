
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


import moment from 'moment';


require("./bootstrap");

window.Vue = require("vue").default;

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
        OperationsList
    }
});
