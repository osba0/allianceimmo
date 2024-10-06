@extends('layouts.app')

@section('content')

@php
    $title = "Gestion des utilisateurs";
    $icon  = "fas fa-users";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Gestion Utilisateurs' => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <user-manager  :agences='@json($agences)' :filiales='@json($filiales)'></user-manager>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
