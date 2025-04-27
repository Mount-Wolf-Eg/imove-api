@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.educational-contents')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.educational-contents_details')}}" pagetitle="{{__('messages.educational-contents')}}" route="{{route('educational-contents.index')}}"/>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <img src="{{ $educationalContent->mainImage->asset_url ?? asset('assets/images/logo-sm.png') }}" class="card-img-top img-fluid mx-auto d-block float-md-left mr-md-4" @style(['max-height:400px'])>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card p-2">
                <div class="card-body">
                    <h5 class="card-title py-2">{{ucfirst($educationalContent->title)}}</h5>
                    <p class="card-text">{{__('messages.joined')}}: {{$educationalContent->created_at?->format('Y-m-d')}}</p>
                    <h5 class="card-title py-2">{{__('messages.details')}}</h5>
                    <div class="row py-2">
                        <div class="col-6">{{__('messages.author')}}</div>
                        <div class="col-6">{{$educationalContent->author?->name}}</div>
                    </div>
                    <div class="row py-2">
                        <div class="col-6">{{__('messages.speciality')}}</div>
                        <div class="col-6">{{$educationalContent->medicalSpeciality?->name}}</div>
                    </div>
                    <div class="row py-2">
                        <div class="col-6">{{__('messages.publish_date')}}</div>
                        <div class="col-6">{{$educationalContent->publish_date ? $educationalContent->publish_date?->format('Y-m-d') : __('messages.not_published')}}</div>
                    </div>
                    <div class="row py-2">
                        <div class="col-6">{{__('messages.likes')}}</div>
                        <div class="col-6">{{count($educationalContent->likes)}}</div>
                    </div>
                    <div class="row py-2">
                        <div class="col-6">{{__('messages.views')}}</div>
                        <div class="col-6">{{$educationalContent->views}}</div>
                    </div>
                    <div class="row py-2">
                        <div class="col-6">{{__('messages.content')}}</div>
                        <div class="col-6">{{$educationalContent->content}}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    {{-- @include('dashboard.educational-contents.image-modal') --}}
@endsection
