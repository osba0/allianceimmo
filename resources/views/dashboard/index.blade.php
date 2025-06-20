@extends('layouts.app')

@section('content')

@php
    $title = "Tableau de bord";
    $breadcrumbs = [
        
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
           
            <div class="boxes">
                <div class="box box1">
                    <h2>Encaissement des <u>3 derniers mois</u> de l'année {{ date('Y') }}</h2>
                    <!--Pie/-->
                    <Bar />
                </div>
                <div class="box box2 newLayout">
                    <div>
                        <h2>Nbre Prioprétaire</h2>
                        <div class="boxState">
                            <a href="{{ route('proprio')}}"  class="totalStatistic">{{$nbreProprietaires}}</a>
                            <!--div class="iconState"><i class="nav-icon fas fa-user-tie"></i></div-->
                        </div>
                    </div>
                    <div>
                        <h2>Nbre Bien</h2>
                        <div class="boxState">
                            <a href="{{ route('biens')}}" class="totalStatistic">{{$nbreBiens}}</a>
                            <!--div class="iconState"><i class="nav-icon fas fa-user-tie"></i></div-->
                        </div>
                    </div>
                </div>
                <div class="box box3 newLayout">
                    <div>
                        <h2>Nbre Locataire</h2>
                        <div class="boxState">
                            <a href="{{ route('locataire')}}"  class="totalStatistic">{{$nbreLocataires}}</a>
                            <!--div class="iconState"><i class="nav-icon fas fa-user"></i></div-->

                        </div>
                    </div>
                    <div>
                        <h2>Nbre local à louer</h2>
                        <div class="boxState">
                            <div class="totalStatistic">{{$biensDisponibles}}</div>
                            <!--div class="iconState"><i class="nav-icon fas fa-home"></i></div-->
                        </div>
                    </div>
                </div>
                 <div class="box box2bis newLayout">
                     <div>
                        <h2>Encaissement du jour</h2>
                        <div class="boxState">
                            <a href="{{ route('locataire')}}"  class="totalStatistic">-</a>
                            <!--div class="iconState"><i class="nav-icon fas fa-user"></i></div-->

                        </div>
                    </div>
                    <div>
                        <h2>Revenu total généré</h2>
                        <div class="boxState">
                            <div class="totalStatistic">{{ number_format($revenuTotal, 2, ',', ' ') }}</div>
                        </div>
                    </div>


                </div>

                <div class="box box5">
                    <div class="d-flex justify-content-between mb-2">
                        <h2>5 derniéres opération(s)</h2>
                        <div>
                            <a href="{{ route('operation') }}" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Paiement</a>
                            <a href="{{ route('charges') }}" class="btn btn-sm btn-danger"><i class="fa fa-plus"></i> Charge</a>
                        </div>
                    </div>

                        <div class="table-responsive lastOperation">
                            <table class="table table-hover">
                                <thead class="bg-white">
                                    <tr>
                                        <th>Sens</th> 
                                        <!--th>Compte</th-->
                                        <th>Type</th> 
                                        <th>Montant Total</th> 
                                        <th>Date</th> 
                                        <th>User</th> 
                                    </tr> 
                                    <tr>
                                        <th colspan="6" class="position-relative p-0">
                                            <div class="loader-line d-none"></div>
                                        </th>
                                    </tr>
                                </thead> 
                                <tbody>

                                    @foreach($dernieresOperations as $operation)
                                    <tr>
                                        <td class="align-middle {{ $operation->oper_sens=='CREDIT'? 'table-success1':'table-danger1' }}">
                                            <label class="badge {{ $operation->oper_sens=='CREDIT'? 'badge-success':'badge-danger' }} mb-0 w-100 py-1 px-2">
                                                {{ $operation->oper_sens }}
                                            </label>
                                        </td>
                                        <!--td class="align-middle">{{ $operation->oper_sens=='CREDIT'? $operation->bail_proprio : $operation->charge_id_proprio}}</td-->
                                        <td class="align-middle">{{ $operation->getType($operation->oper_type) }}</td>
                                        <td class="align-middle">{{ number_format($operation->oper_montant, 0, ',', ' ')  }}</td>
                                        <td class="align-middle">{{ $operation->created_at }}</td>
                                        <td class="align-middle">{{ $operation->oper_user }}</td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            <div class="text-align text-align d-flex justify-content-center">
                                <a class="btn-sm btn btn-primary" href="{{route('operationList')}}">Voir tout <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>
                     <div class="box box4" style="height: 350px">
                        <h2>Cartographie des biens gérés</h2>
                        <bien-carte></bien-carte>
                    </div>
                <div class="box box4bis d-none">
                    <h2>Nombre de biens disponibles à la location</h2>
                    <div class="boxState">
                        <div class="totalStatistic">{{$biensDisponibles}}</div>
                        <div class="iconState"><i class="nav-icon fas fa-home"></i></div>
                    </div>
                </div>
               
                </div>
           
        </div>
    </div>
</div>
@endsection
