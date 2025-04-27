@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.edit_educational-contents')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.edit_educational-contents')}}" pagetitle="{{__('messages.educational-contents')}}" route="{{route('educational-contents.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.educational-contents.partials.__form', ['action' => ['educational-contents.update', $educationalContent->id], 'method' => 'PUT'])
            {{-- @include('dashboard.educational-contents.image-modal') --}}
        </div>
    </div>
@endsection
