@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.technical-support')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.details')}}" pagetitle="{{__('messages.technical-support')}}" route="{{route('technical-support.index')}}"/>
    <div class="row">

        <div class="col-md-12">
            <div class="card p-2">
                <div class="card-body">
                    <h5 class="card-title py-2">{{ucfirst($technicalSupport->topic)}}</h5>    
                    <p class="card-text">{{__('messages.created')}}: {{$technicalSupport->created_at->format('Y-m-d')}}</p>
                    <h5 class="card-title py-2">{{__('messages.details')}}</h5>
                    
                    @if ($technicalSupport->doctor_id)
                        <div class="row py-2">
                            <div class="col-2">{{__('messages.type')}}</div>
                            <div class="col-10">{{__('messages.doctor')}}</div>
                        </div>
                        <div class="row py-2">
                            <div class="col-2">{{__('messages.name')}}</div>
                            <div class="col-10">{{$technicalSupport->doctor?->user->name}}</div>
                        </div>
                        <div class="row py-2">
                            <div class="col-2">{{__('messages.phone')}}</div>
                            <div class="col-10">{{$technicalSupport->doctor?->user->phone}}</div>
                        </div>
                    @else
                        <div class="row py-2">
                            <div class="col-2">{{__('messages.type')}}</div>
                            <div class="col-10">{{__('messages.patient')}}</div>
                        </div>
                        <div class="row py-2">
                            <div class="col-2">{{__('messages.name')}}</div>
                            <div class="col-10">{{$technicalSupport->user?->name}}</div>
                        </div>
                        <div class="row py-2">
                            <div class="col-2">{{__('messages.phone')}}</div>
                            <div class="col-10">{{$technicalSupport->user?->phone}}</div>
                        </div>
                    @endif
                    
                    <div class="row py-2">
                        <div class="col-2">{{__('messages.email')}}</div>
                        <div class="col-10">{{$technicalSupport->email}}</div>
                    </div>

                    <div class="row py-2">
                        <div class="col-2">{{__('messages.subject')}}</div>
                        <div class="col-10">{{$technicalSupport->topic}}</div>
                    </div>
                    <div class="row py-2">
                        <div class="col-2">{{__('messages.message')}}</div>
                        <div class="col-10">{{$technicalSupport->message}}</div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
