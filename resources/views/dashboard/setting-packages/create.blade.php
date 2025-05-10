@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.add_setting-package')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.add_setting-package')}}" pagetitle="{{__('messages.setting-packages')}}" route="{{route('setting-packages.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.setting-packages.partials.__form', ['action' => 'setting-packages.store', 'method' => 'POST'])
        </div>
    </div>
@endsection
