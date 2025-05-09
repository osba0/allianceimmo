@extends('layouts.app')

@section('content')

@php
    $title = "Templates";
    $icon  = "fas fa-share-alt";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Edit Template' => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
             <div class="px-4 py-4 bg-white rounded-lg">

                @if($type == \App\Models\Template::TYPE_QUITTANCE_LOYER)
                    <quittance-loyer-template
                        :variables="{{ json_encode($variables) }}"
                        preview-url="{{ route('templates.quittance-loyer.preview') }}"
                        list-url="{{ route('templates.index', ['type' => $type]) }}"
                        save-url="{{ route('templates.store', ['type' => $type]) }}"
                        :template-data='@json($template)'
                        model-lines-const="{{ App\Services\Template\TemplateService::MODEL_LINES }}"
                    ></quittance-loyer-template>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
