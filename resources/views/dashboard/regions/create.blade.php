@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.add_region')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.add_region')}}"
                  pagetitle="{{__('messages.regions')}}" route="{{route('regions.index')}}"/>

    <div class="row">
        <div class="col-md-12">
            @include('dashboard.regions.partials.__form', ['action' => 'regions.store', 'method' => 'POST'])
        </div>
    </div>
@endsection
