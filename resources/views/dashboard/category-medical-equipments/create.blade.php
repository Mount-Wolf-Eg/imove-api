@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.add_equipment-category')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.add_equipment-category')}}"
                  pagetitle="{{__('messages.equipment-categories')}}" route="{{route('category-medical-equipments.index')}}"/>

    <div class="row">
        <div class="col-md-12">
            @include('dashboard.category-medical-equipments.partials.__form', ['action' => 'category-medical-equipments.store', 'method' => 'POST'])
        </div>
    </div>
@endsection
