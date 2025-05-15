@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.add_setting-consultation')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.add_setting-consultation')}}" pagetitle="{{__('messages.setting-consultations')}}" route="{{route('setting-consultations.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.setting-consultations.partials.__form', ['action' => 'setting-consultations.store', 'method' => 'POST'])
        </div>
    </div>
@endsection
