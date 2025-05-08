@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.package')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.package_details')}}" pagetitle="{{__('messages.packages')}}" route="{{route('packages.index')}}"/>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <img src="{{ $package->mainImage->asset_url ?? asset('assets/images/logo-sm.png') }}" class="card-img-top img-fluid mx-auto d-block float-md-left mr-md-4" @style(['max-height:400px'])>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card p-2">
                <div class="card-body">
                    <h5 class="card-title py-2">{{ucfirst($package->name)}}</h5>
                    <p class="card-text">{{__('messages.created_at')}}: {{$package->created_at?->format('Y-m-d')}}</p>
                    <h5 class="card-title py-2">{{__('messages.details')}}</h5>
                    <div class="row py-2">
                        <div class="col-6">{{__('messages.name')}}</div>
                        <div class="col-6">{{$package->name}}</div>
                    </div>
                    <div class="row py-2">
                        <div class="col-6">{{__('messages.description')}}</div>
                        <div class="col-6">{{$package->description}}</div>
                    </div>
                    <div class="row py-2">
                        <div class="col-6">{{__('messages.num_of_sessions')}}</div>
                        <div class="col-6">{{$package->num_of_sessions}}</div>
                    </div>
                    <div class="row py-2">
                        <div class="col-6">{{__('messages.duration')}}</div>
                        <div class="col-6">{{$package->duration}}</div>
                    </div>
                    <div class="row py-2">
                        <div class="col-6">{{__('messages.price')}}</div>
                        <div class="col-6">{{$package->price}}</div>
                    </div>
                    <div class="row py-2">
                        <div class="col-6">{{__('messages.is_active')}}</div>
                        <div class="col-6">
                            @if($package->is_active)
                                <span class="badge bg-success">{{__('messages.active')}}</span>
                            @else
                                <span class="badge bg-danger">{{__('messages.inactive')}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="row py-2">
                        <div class="col-6">{{__('messages.doctor_name')}}</div>
                        <div class="col-6">{{$package->user?->name}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('dashboard.packages.image-modal')
@endsection
