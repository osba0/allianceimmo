@extends('layouts.app')

@section('content')

<?php
    use App\Models\Proprietaires;
    use App\Models\Locataires;
    $title = "Encaissements";
    $icon  = "fas fa-male";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Encaissements' => ['url' => '']
    ];
    $proprietaires = Proprietaires::get();
    $locataires = Locataires::get();
?>

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <encaissements :list-proprio="{{ json_encode($proprietaires) }}" :list-locataires="{{ json_encode($locataires) }}" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
