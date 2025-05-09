@extends('layouts.app')

@section('content')

@php
    $title = "Templates";
    $icon  = "fas fa-share-alt";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Template' => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
             <div class="px-4 py-4 bg-white rounded-lg">
                <div class="row justify-content-center mb-3">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="h1">{{ $title }}</div>
                            </div>
                        </div>
                        <div class="row py-4">
                            <div class="col-lg-12">
                                <template-data-table
                                    url="{{ route('templates.get-list') }}"
                                    :type="{{ $type }}"
                                    :default-ids="{{ json_encode(array_keys(\App\Services\Template\TemplateService::$mappingDefaultTemplates)) }}"
                                ></template-data-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
