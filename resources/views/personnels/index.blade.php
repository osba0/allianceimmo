@extends('layouts.app')

@section('content')

@php
    $title = "Personnels";
    $icon  = "fas fa-users";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Personnels' => ['url' => '']
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
