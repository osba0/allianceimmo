@extends('layouts.app')

@section('content')

<?php
    use App\Models\Proprietaires;
    use App\Models\Biens;
    $title = "Versements";
    $icon  = "fas fa-male";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Versements' => ['url' => '']
    ];
    $proprietaires = Proprietaires::get();
    $biens = Biens::get();
?>

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <versement-proprio :list-proprio="{{ json_encode($proprietaires) }}" :biens="{{ json_encode($biens) }}" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
