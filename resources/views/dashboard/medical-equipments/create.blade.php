@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.add_medicalEquipment')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.add_medicalEquipment')}}"
                  pagetitle="{{__('messages.medicalEquipments')}}" route="{{route('medical-equipments.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.medical-equipments.partials.__form', ['action' => 'medical-equipments.store', 'method' => 'POST'])
        </div>
    </div>
@endsection
