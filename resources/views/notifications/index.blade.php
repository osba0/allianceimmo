@extends('layouts.app')

@section('content')

@php
    $title = "📢 Notifications";
    $icon  = "fas fa-home";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Notification' => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <notifications></notifications>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
