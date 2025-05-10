@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.add_educational-contents')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.add_educational-contents')}}" pagetitle="{{__('messages.educational-contents')}}" route="{{route('educational-contents.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.educational-contents.partials.__form', ['action' => 'educational-contents.store', 'method' => 'POST'])
        </div>
    </div>
@endsection
