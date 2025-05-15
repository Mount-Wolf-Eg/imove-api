@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.edit_setting-consultation')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.edit_setting-consultation')}}" pagetitle="{{__('messages.setting-consultations')}}" route="{{route('setting-consultations.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.setting-consultations.partials.__form', ['action' => ['setting-consultations.update', $settingConsultation->id], 'method' => 'PUT'])
        </div>
    </div>
@endsection
