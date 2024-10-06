@extends('layouts.app')

@section('content')

@php
    $title = "Preferences";
    $icon  = "fas fa-cogs";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Preference' => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <role-manager></role-manager>
                    <permission-manager></permission-manager>

                </div>
            </div>
        </div>
         <div class="col-md-6">
             <div class="card">
                <div class="card-body">
                    <role-permission-assigner></role-permission-assigner>
                 </div>
             </div>
         </div>
    </div>
</div>
@endsection
