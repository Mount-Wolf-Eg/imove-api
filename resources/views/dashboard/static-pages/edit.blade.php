@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.edit_static-page')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.edit_static-page')}}"
                  pagetitle="{{__('messages.static-page')}}" route="{{route('static-pages.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.static-pages.partials.__form', ['action' => ['static-pages.update', $staticPage->id], 'method' => 'PUT'])
        </div>
    </div>
@endsection
