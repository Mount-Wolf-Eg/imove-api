@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.edit_university')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.edit_university')}}"
                  pagetitle="{{__('messages.universities')}}" route="{{route('universities.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.universities.partials.__form', ['action' => ['universities.update', $university->id], 'method' => 'PUT'])
        </div>
    </div>
@endsection
