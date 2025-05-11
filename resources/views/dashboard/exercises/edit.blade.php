@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.edit_exercises')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.edit_exercises')}}" pagetitle="{{__('messages.exercises')}}" route="{{route('exercises.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.exercises.partials.__form', ['action' => ['exercises.update', $exercise->id], 'method' => 'PUT'])
            {{-- @include('dashboard.educational-contents.image-modal') --}}
        </div>
    </div>
@endsection
