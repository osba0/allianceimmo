@extends('layouts.app')

@section('content')

@php
    $title = "Rapport";
    $icon  = "fas fa-share-alt";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Rapport' => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                   A venir
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
