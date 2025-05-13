@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.university')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.university_details')}}" pagetitle="{{__('messages.universities')}}" route="{{route('universities.index')}}"/>
    <div class="row">
        
        <div class="col-md-8">
            <div class="card p-2">
                <div class="card-body">
                    <h5 class="card-title py-2">{{ucfirst($university->name)}}</h5>
                    <p class="card-text">{{__('messages.created')}}: {{$university->created_at->format('Y-m-d')}}</p>
                    <h5 class="card-title py-2">{{__('messages.details')}}</h5>
                    
                    
                </div>
            </div>
        </div>
    </div>
@endsection
