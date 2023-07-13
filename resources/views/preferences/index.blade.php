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
