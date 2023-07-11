@extends('layouts.app')

@section('content')

@php
    $title = "Bail";
    $icon  = "fas fa-gavel";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Bail' => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <Bail :list-proprio="{{ json_encode($proprietaires) }}" :list-locataire="{{ json_encode($listLocataire) }}"></Bail>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
