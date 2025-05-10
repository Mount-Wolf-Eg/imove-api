@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.medical-equipment')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.medical-equipment_details')}}" pagetitle="{{__('messages.medical-equipments')}}" route="{{route('medical-equipments.index')}}"/>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <img src="{{$medicalEquipment->photo->asset_url ?? asset('assets/images/users/user-dummy-img.jpg')}}" class="card-img-top img-fluid mx-auto d-block float-md-left mr-md-4" @style(['max-height:400px'])>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card p-2">
                <div class="card-body">
                    <h5 class="card-title py-2">{{ucfirst($medicalEquipment->name)}}</h5>
                    <p class="card-text">{{__('messages.created')}}: {{$medicalEquipment->created_at->format('Y-m-d')}}</p>
                    <h5 class="card-title py-2">{{__('messages.details')}}</h5>
                    <div class="row py-2">
                        <div class="col-6">{{__('messages.type')}}</div>
                        <div class="col-6">{{$medicalEquipment->category->name}}</div>
                    </div>
                    <div class="row py-2">
                        <div class="col-6">{{__('messages.link')}}</div>
                        <div class="col-6">{{$medicalEquipment->link}}</div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection
