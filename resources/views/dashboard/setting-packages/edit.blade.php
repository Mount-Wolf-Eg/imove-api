@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.edit_setting-package')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.edit_setting-package')}}" pagetitle="{{__('messages.setting-packages')}}" route="{{route('setting-packages.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.setting-packages.partials.__form', ['action' => ['setting-packages.update', $settingPackage->id], 'method' => 'PUT'])
        </div>
    </div>
@endsection
