@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.add_seniority')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.add_seniority')}}"
                  pagetitle="{{__('messages.seniorities')}}" route="{{route('seniorities.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.seniorities.partials.__form', ['action' => 'seniorities.store', 'method' => 'POST'])
        </div>
    </div>
@endsection
