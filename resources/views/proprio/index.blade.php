@extends('layouts.app')

@section('content')
<?php

    use App\Models\Proprietaires;
    $title = "🏠 Propriétaires";
    $icon  = "fas fa-key";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Propriétaires' => ['url' => '']
    ];
    $proprietaires = Proprietaires::get();

?>

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <Proprio  :list-proprio="{{ json_encode($proprietaires) }}" env="{{ env('APP_URL') }}" ></Proprio>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
