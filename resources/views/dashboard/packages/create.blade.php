@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.add_package')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.add_package')}}" pagetitle="{{__('messages.packages')}}" route="{{route('packages.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.packages.partials.__form', ['action' => 'packages.store', 'method' => 'POST'])
        </div>
    </div>
@endsection
