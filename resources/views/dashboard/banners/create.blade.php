@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.add_banner')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.add_banner')}}" pagetitle="{{__('messages.exercises')}}" route="{{route('banners.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.banners.partials.__form', ['action' => 'banners.store', 'method' => 'POST'])
        </div>
    </div>
@endsection
