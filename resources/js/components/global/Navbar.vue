<template>
    <!-- Navbar -->
    <nav class="main-header navbar fixed-top navbar-expand navbar-white navbar-light">
         
        <div class="d-flex align-items-center justify-content-center">
            <img src="/assets/images/logo_ab_immo_rounded_1.png" height="50" class="mr-2">
            <span class="brand-text h3 mb-0">ALLIANCE BAZICS IMMO</span>
        </div>
        <!-- Left navbar links -->
        <ul class="navbar-nav d-none">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="/home" class="nav-link">Home</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="/without/breadcrumbs" class="nav-link">Without breadcrumbs</a>
            </li>
        </ul>

        <!-- SEARCH FORM -->
        <form class="form-inline ml-3 d-none">
            <div class="input-group input-group-sm">
                <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                    <i class="fas fa-search"></i>
                </button>
                </div>
            </div>
        </form>

        <div class="ml-md-auto d-flex align-items-center">
            <div class="image"><img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg" height="40" alt="User Image" class="img-circle elevation-2"></div>
            <div class="dropdown mr-3">
              <button class="border-0 bg-transparent btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                Oumar
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="#">Mon Compte</a>
                <a class="dropdown-item" @click="logout()">Déconnexion</a>
              </div>
            </div>
               <!-- Right navbar links -->
                <ul class="navbar-nav mr-4 ml-0 pl-0">
                    <button :disabled="disable" @click="logout()" title="Déconnexion" class="btn btn-danger"><i class="fas fa-power-off"></i></button>
                </ul>
        </div>

       
 

    </nav>
    <!-- /.navbar -->
</template>

<script>
import axios from 'axios'
import Errors from '../partials/Errors.vue'

export default {
    components: { Errors },
    name: "Navbar",
    data() {
        return {
            disable: false
        }
    },
    methods: {
        logout() {
            // Disable the logout button
            this.disable = true
            // Get the CSRF token
            let token = document.head.querySelector('meta[name="csrf-token"]').getAttribute('content')
            // Log the user out
            axios.post('/logout', token)
            .then(() => {
                console.log('logged out successfully')
                window.location.href = '/'
            })
            .catch(() => /* Enable the logout button */ this.disable = false)
        }
    }
}
</script>

<style>
    .logo {
        height: 45px;
        width: 250px;
    }
    .logo-mobile {
        height: 60px;
        width: 200px;
    }
    #form {
        background: white;
        padding: 25px;
        border-radius: 14px;
        border: 2px solid #14756a23;
    }
</style>
