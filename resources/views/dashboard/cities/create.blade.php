@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.add_city')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.add_city')}}"
                  pagetitle="{{__('messages.cities')}}" route="{{route('cities.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.cities.partials.__form', ['action' => 'cities.store', 'method' => 'POST'])
        </div>
    </div>
@endsection
