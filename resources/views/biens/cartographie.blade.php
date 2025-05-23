@extends('layouts.app')

@section('content')

@php
    $title = "Cartographie";
    $icon  = "fas fa-map";
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
                    <div style="width: calc(100% - 0); height: 90vh">
                        <bien-carte></bien-carte>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
