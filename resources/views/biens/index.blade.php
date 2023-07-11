@extends('layouts.app')

@section('content')

@php
    $title = "Bien / Immeuble";
    $icon  = "fas fa-building";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Bien & Immeuble' => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <bien-immeuble :list-proprio="{{ json_encode($proprietaires) }}"></bien-immeuble>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
