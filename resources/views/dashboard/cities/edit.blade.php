@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.edit_city')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.edit_city')}}"
                  pagetitle="{{__('messages.cities')}}" route="{{route('cities.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.cities.partials.__form', ['action' => ['cities.update', $city->id], 'method' => 'PUT'])
        </div>
    </div>
@endsection
