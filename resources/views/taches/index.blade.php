@extends('layouts.app')

@section('content')

@php
    $title = "Tâches Automatisées";
    $icon  = "fas fa-share-alt";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Tâches Automatisées' => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                   <Taches-automisees />

                </div>
            </div>
        </div>
        <div class="col-md-12">

        </div>
    </div>
</div>
@endsection
