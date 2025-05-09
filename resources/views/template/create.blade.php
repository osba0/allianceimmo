@extends('layouts.app')

@section('content')
    <div class="px-4 py-4 bg-white rounded-lg">
        @if($type == \App\Models\Template::TYPE_SHIPPING)
            <shipping-email-show
                :is-create="true"
                :variables="{{ json_encode($variables) }}"
                preview-url="{{ route('templates.shipping-email.send') }}"
                mails-list-url="{{ route('templates.index', ['type' => $type]) }}"
                save-url="{{ route('templates.store', ['type' => $type]) }}"
                :template-data='@json($template)'
            ></shipping-email-show>
        @endif
        @if($type == \App\Models\Template::TYPE_DELIVERY_NOTE)
            <delivery-note-template
                :is-create="true"
                :variables="{{ json_encode($variables) }}"
                preview-url="{{ route('templates.delivery-note.preview') }}"
                list-url="{{ route('templates.index', ['type' => $type]) }}"
                save-url="{{ route('templates.store', ['type' => $type]) }}"
                :template-data='@json($template)'
                model-lines-const="{{ App\Services\Template\TemplateService::MODEL_LINES }}"
            ></delivery-note-template>
        @endif
    </div>
@endsection
