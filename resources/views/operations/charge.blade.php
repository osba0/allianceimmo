@extends('layouts.app')

@section('content')

@php
    $title = "Charges & Frais";
    $icon  = "fas fa-bolt";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Opérations' => ['url' => ''],
        'Charges & Frais' => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @php $type_charges = \App\Models\Operations::getListCharges(); @endphp
                    <Charges :list-proprio="{{ json_encode($proprietaires) }}" :type-charge="{{ json_encode($type_charges) }}" :list-personnel="{{ json_encode($personnels) }}" :current-agence="{{ json_encode($agence) }}"></Charges>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
