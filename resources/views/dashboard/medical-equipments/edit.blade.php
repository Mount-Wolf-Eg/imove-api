@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.edit_medicalEquipment')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.edit_medicalEquipment')}}"
                  pagetitle="{{__('messages.medicalEquipments')}}" route="{{route('medical-equipments.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.medical-equipments.partials.__form', ['action' => ['medical-equipments.update', $medicalEquipment->id], 'method' => 'PUT'])
        </div>
    </div>
@endsection
