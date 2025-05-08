@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.edit_package')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.edit_package')}}" pagetitle="{{__('messages.packages')}}" route="{{route('packages.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.packages.partials.__form', ['action' => ['packages.update', $package->id], 'method' => 'PUT'])
            @include('dashboard.packages.image-modal')
        </div>
    </div>
@endsection
