@extends('layouts.app')

@section('content')

@php
    $title = "Propriétaires";
    $icon  = "fas fa-key";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Propriétaires' => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <Proprio></Proprio>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
