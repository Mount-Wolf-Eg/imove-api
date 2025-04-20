@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.add_static-page')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.add_static-page')}}"
                  pagetitle="{{__('messages.static-pages')}}" route="{{route('static-pages.index')}}"/>

    <div class="row">
        <div class="col-md-12">
            @include('dashboard.static-pages.partials.__form', ['action' => 'static-pages.store', 'method' => 'POST'])
        </div>
    </div>
@endsection
