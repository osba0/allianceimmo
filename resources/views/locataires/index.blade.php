@extends('layouts.app')

@section('content')

<?php
    use App\Models\Locataires;
    $title = "Locataires";
    $icon  = "fas fa-male";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Locataires' => ['url' => '']
    ];
    $locataires = Locataires::get();
?>

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <Locataires :list-locataire="{{ json_encode($locataires) }}" env="{{ env('APP_URL') }}"></Locataires>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
