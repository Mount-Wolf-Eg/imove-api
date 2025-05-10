@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.edit_equipment-category')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.edit_equipment-category')}}"
                  pagetitle="{{__('messages.equipment-categories')}}" route="{{route('category-medical-equipments.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            @include('dashboard.category-medical-equipments.partials.__form', ['action' => ['category-medical-equipments.update', $equipmentCategory->id], 'method' => 'PUT'])
        </div>
    </div>
@endsection
