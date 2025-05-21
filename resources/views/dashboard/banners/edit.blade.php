@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.edit_banner')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.edit_banner')}}" pagetitle="{{__('messages.banners')}}" route="{{route('banners.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.banners.partials.__form', ['action' => ['banners.update', $banner->id], 'method' => 'PUT'])
        </div>
    </div>
@endsection
