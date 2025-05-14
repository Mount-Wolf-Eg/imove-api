@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.city')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.city_details')}}" pagetitle="{{__('messages.cities')}}" route="{{route('cities.index')}}"/>
    <div class="row">
        
        <div class="col-md-8">
            <div class="card p-2">
                <div class="card-body">
                    <h5 class="card-title py-2">{{ucfirst($city->name)}}</h5>
                    <p class="card-text">{{__('messages.created')}}: {{$city->created_at->format('Y-m-d')}}</p>
                    <h5 class="card-title py-2">{{__('messages.details')}}</h5>
          
                    <div class="row py-2">
                        <div class="col-2">{{__('messages.region')}}</div>
                        <div class="col-10">{{$city->region->name?? '------'}}</div>
                    </div>
                    

                </div>
            </div>
        </div>
    </div>
@endsection
