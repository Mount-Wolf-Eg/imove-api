@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.add_university')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.add_university')}}"
                  pagetitle="{{__('messages.universities')}}" route="{{route('universities.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.universities.partials.__form', ['action' => 'universities.store', 'method' => 'POST'])
        </div>
    </div>
@endsection
