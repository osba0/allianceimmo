<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
     <link rel="icon" type="image/png" href="{{ url('/assets/images/logo-login.png') }}">
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ url('css/main.css') }}" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Adminlte Style Integration -->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ url('assets/adminlte/dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ url('assets/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    
    @yield('styles')

    <title>{{ config('app.name', 'Laravel') }}</title>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper" id="app">
         <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <!--a href="/" class="brand-link py-2 d-flex">
                <img src="/assets/images/logo_ab_immo_rounded_1.png" class="logo_nav"/>
                <div class="pl-2">
                    <span class="logo-abreviation d-none h2 mb-0 text-center font-weight-bold">ABI</span>
                    <span class="brand-text h2 font-weight-bold">ALLIANCE BAZICS IMMO</span>
                 </div>

            </a-->

            <a href="/" class="brand-link text-center py-2">
                <img src="{{ url('/assets/images/logo-login.png') }}" class="logo_nav"/>
                <div class="pl-2 text-center">
                    <span class="logo-abreviation d-none h2 mb-0 text-center font-weight-bold">ABI</span>
                    <span class="brand-text h3 font-weight-bold text-uppercase">{{ config('app.name', 'Laravel') }}.net</span>
                 </div>

            </a>
            
            <!-- Sidebar -->
            <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <!--div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                <img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name }}</a>
                </div>
            </div-->

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
                        with font-awesome or any other icon font library -->
                    <li class="nav-item">
                        <a href="/home" class="nav-link {{Request::route()->getName()=='home'? 'active':''}}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Tableau de bord</p>
                        </a>
                    </li>
                    <li>
                        <div class="menu-group">
                            <span  class="title-wrapper">
                                <span class="{{ in_array(Request::route()->getName(), ['proprio', 'biens', 'locataire', 'gerance', 'bail', 'rapport', 'personnel'])? 'active':''}}">Administration</span>
                            </span>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="/proprio/list" class="nav-link {{Request::route()->getName()=='proprio'? 'active':''}}">
                            <i class="nav-icon fas fa-key"></i>
                            <p>Propriétaires</p>
                        </a>
                    </li>
                     <li class="nav-item">
                        <a href="/bien/index" class="nav-link  {{Request::route()->getName()=='biens'? 'active':''}}">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Bien / Immeuble</p>
                        </a>
                    </li>
                     <li class="nav-item">
                        <a href="/locat/list" class="nav-link {{Request::route()->getName()=='locataire'? 'active':''}}">
                            <i class="nav-icon fas fa-male"></i>
                            <p>Locataires</p>
                        </a>
                    </li>
                     <li class="nav-item">
                        <a href="/gerance/index" class="nav-link {{Request::route()->getName()=='gerance'? 'active':''}}">
                            <i class="nav-icon fas fa-balance-scale"></i>
                            <p>Mandat de Gérance</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/bail/index" class="nav-link {{Request::route()->getName()=='bail'? 'active':''}}">
                            <i class="nav-icon fas fa-gavel"></i>
                            <p>Bail</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('rapport')}}" class="nav-link {{Request::route()->getName()=='rapport'? 'active':''}}">
                            <i class="nav-icon fas fa-share-alt"></i>
                            <p>Rapports</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('personnel')}}" class="nav-link {{Request::route()->getName()=='personnel'? 'active':''}}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Personnels</p>
                        </a>
                    </li>
                     <li>
                        <div class="menu-group">
                            <span  class="title-wrapper">
                                <span class="{{ in_array(Request::route()->getName(), ['operationList', 'operation', 'charges'])? 'active':''}}">Opérations</span>
                            </span>
                        </div>
                    </li>
                     <li class="nav-item">
                        <a href="{{ route('operationList') }}" class="nav-link {{Request::route()->getName()=='operationList'? 'active':''}}">
                            <i class="nav-icon fas fa-list"></i>
                            <p>Liste des Opérations</p>
                        </a>
                    </li>
                     <li class="nav-item">
                        <a href="{{ route('operation') }}" class="nav-link {{Request::route()->getName()=='operation'? 'active':''}}">
                            <i class="nav-icon fas fa-random"></i>
                            <p>Loyers & Paiement</p>
                        </a>
                    </li>
                     <li class="nav-item">
                        <a href="{{ route('charges') }}" class="nav-link {{Request::route()->getName()=='charges'? 'active':''}}">
                            <i class="nav-icon fas fa-bolt"></i>
                            <p>Charge & Frais</p>
                        </a>
                    </li>
                     <li>
                        <div class="menu-group">
                            <span  class="title-wrapper">
                                <span class="{{ in_array(Request::route()->getName(), ['preference', 'compte'])? 'active':''}}">Configurations</span>
                            </span>
                        </div>
                    </li>
                      <li class="nav-item">
                        <a href="{{ route('preference') }}" class="nav-link {{Request::route()->getName()=='preference'? 'active':''}}">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>Préférences</p>
                        </a>
                    </li>
                     <li class="nav-item">
                        <a href="{{ route('compte') }}" class="nav-link  {{Request::route()->getName()=='compte'? 'active':''}}">
                            <i class="nav-icon fas fa-address-card"></i>
                            <p>Mon Compte</p>
                        </a>
                    </li>
                   
                   
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>
        <nav class="main-header navbar fixed-top navbar-expand navbar-white navbar-light">
         
        <div class="d-flex align-items-center justify-content-center">
            <img src="{{ url('/assets/images/logo_ab_immo_rounded_1.png') }}" height="50" class="mr-2">
            <span class="brand-text h3 mb-0">ALLIANCE BAZICS IMMO</span>
            <solde-top-barre></solde-top-barre>
            
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

        <div class="ml-md-auto d-flex align-items-center mr-3">
            <div class="mr-3">
                <a href="#" class="text-info h3 mb-0"><i class="fas fa-bell"></i></a>
            </div>
            <span class="badge badge-primary">Profil</span> 
            <span class="mx-1 text-white">:</span>
            <span class="badge badge-info mr-2">ADMIN</span>
            <div class="image"><img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg" height="40" alt="User Image" class="img-circle elevation-2"></div>
            <div class="dropdown mr-3">
              <button class="border-0 bg-transparent btn btn-default dropdown-toggle text-white" type="button" data-toggle="dropdown" aria-expanded="false">
                {{ auth()->user()->name }}
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="#">Mon Compte</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a class="dropdown-item" href="{{ route('logout') }}">Déconnexion</a>
                </form>
              </div>
            </div>
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <a href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="text-white h4 mb-0">
                       <i class="fas fa-power-off"></i>
                    </a>
                </form>
        </div>

       


    </nav>
        <Container :user="{{ auth()->user()->tojson() }}" domain="{{ env('APP_URL') }}" current_route="{{ Request::route()->getName() }}">
            <template v-slot:breadcrumbs>
                @include('partials.breadcrumbs', ['title' => $title ?? '', 'items' => $breadcrumbs ?? []])
            </template>
            <template v-slot:content>
                @yield('content')
            </template>
        </Container>
    </div>
    
</body>
</html>
