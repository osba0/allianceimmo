<template>
    <div>
        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="/" class="brand-link py-2">
                <span class="logo-abreviation d-none h2 mb-0 text-center font-weight-bold">ABI</span>
                <span class="brand-text h2 font-weight-bold">ALLIANCE BAZICS IMMO</span>
            </a>
            <!-- Sidebar -->
            <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                <img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                <a href="#" class="d-block">{{ user.name }}</a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
                        with font-awesome or any other icon font library -->
                    <li class="nav-item">
                        <a href="/home" class="nav-link" :class="[current_route=='home'? 'active':'']">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Tableau de bord</p>
                        </a>
                    </li>
                    <li>
                        <div class="menu-group">
                            <span  class="title-wrapper">
                                <span>Administration</span>
                            </span>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="/propio/list" class="nav-link" :class="[current_route=='proprio'? 'active':'']">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Propriétaires</p>
                        </a>
                    </li>
                     <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Locataires</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Contrats</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Rapports</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Personnels</p>
                        </a>
                    </li>
                     <li>
                        <div class="menu-group">
                            <span  class="title-wrapper">
                                <span>Opérations</span>
                            </span>
                        </div>
                    </li>
                     <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Loyers & Paiement</p>
                        </a>
                    </li>
                     <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Charge & Frais</p>
                        </a>
                    </li>
                     <li>
                        <div class="menu-group">
                            <span  class="title-wrapper">
                                <span>Configurations</span>
                            </span>
                        </div>
                    </li>
                      <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Préférences</p>
                        </a>
                    </li>
                     <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Mon Compte</p>
                        </a>
                    </li>
                   
                   
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>
    </div>
</template>

<script>
export default {
    name: "Sidebar",
    props: ['user', 'current_route']
}
</script>
