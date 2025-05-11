@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.add_exercise')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.add_exercise')}}" pagetitle="{{__('messages.exercises')}}" route="{{route('exercises.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.exercises.partials.__form', ['action' => 'exercises.store', 'method' => 'POST'])
        </div>
    </div>
@endsection
