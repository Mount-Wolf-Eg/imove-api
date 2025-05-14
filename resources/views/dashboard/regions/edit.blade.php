@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.edit_region')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.edit_region')}}"
                  pagetitle="{{__('messages.regions')}}" route="{{route('regions.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.regions.partials.__form', ['action' => ['regions.update', $region->id], 'method' => 'PUT'])
        </div>
    </div>
@endsection
