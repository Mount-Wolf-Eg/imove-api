@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.profile')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.profile')}}" pagetitle="{{__('messages.imove')}}" route="{{route('dashboard')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.profile.partials.__form', ['action' => ['profile.update'], 'method' => 'POST'])
        </div>
    </div>
@endsection
