@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.exercises')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.exercise_details')}}" pagetitle="{{__('messages.exercises')}}" route="{{route('exercises.index')}}"/>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                @if($exercise->media && Str::endsWith($exercise->media->asset_url, ['.mp4', '.webm', '.ogg']))
                    <video controls style="max-height:400px; width: 100%;" class="mx-auto d-block float-md-left mr-md-4">
                        <source src="{{ $exercise->media->asset_url }}" type="video/mp4">
                        المتصفح لا يدعم تشغيل الفيديو.
                    </video>
                @else
                    <img src="{{ $exercise->media->asset_url ?? asset('assets/images/logo-sm.png') }}" class="card-img-top img-fluid mx-auto d-block float-md-left mr-md-4" style="max-height:400px;">
                @endif
            </div>
            
            <div class="col-md-11">
                <div class="card">
                    <img src="{{ $exercise->mainImage->asset_url ?? asset('assets/images/logo-sm.png') }}" class="card-img-top img-fluid mx-auto d-block float-md-left mr-md-4" @style(['max-height:400px'])>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card p-2">
                <div class="card-body">
                    <h5 class="card-title py-2">{{ucfirst($exercise->name)}}</h5>
                    <p class="card-text">{{__('messages.created_at')}}: {{$exercise->created_at?->format('Y-m-d')}}</p>
                    <h5 class="card-title py-2">{{__('messages.details')}}</h5>
                    <div class="row py-2">
                        <div class="col-2">{{__('messages.name')}}</div>
                        <div class="col-10">{{$exercise->name}}</div>
                    </div>
                    <div class="row py-2">
                        <div class="col-2">{{__('messages.brief')}}</div>
                        <div class="col-10">{{$exercise->brief}}</div>
                    </div>
                    <div class="row py-2">
                        <div class="col-2">{{__('messages.description')}}</div>
                        <div class="col-10">{{$exercise->description}}</div>
                    </div>
                    <div class="row py-2">
                    <div class="col-2">{{__('messages.speciality')}}</div>
                        <div class="col-10">
                            @foreach($exercise->medicalSpecialities as $speciality)
                                <span class="badge bg-info text-white">{{ $speciality->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    {{-- <div class="row py-2">
                        <div class="col-6">{{__('messages.speciality')}}</div>
                        <div class="col-6">{{$exercise->medicalSpeciality?->name}}</div>
                    </div> --}}
                </div>
            </div>
        </div>

    </div>
    {{-- @include('dashboard.educational-contents.image-modal') --}}
@endsection
