@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.edit_seniority')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.edit_seniority')}}"
                  pagetitle="{{__('messages.seniority')}}" route="{{route('seniorities.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.seniorities.partials.__form', ['action' => ['seniorities.update', $seniority->id], 'method' => 'PUT'])
        </div>
    </div>
@endsection
