@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.manage_equipment-categories')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.manage_equipment-categories')}}" pagetitle="{{__('messages.equipment-categories')}}" route="{{route('category-medical-equipments.index')}}"/>
    <div class="d-flex justify-content-sm-end">
        <a href="{{route('category-medical-equipments.create')}}">
            <i class="bi bi-plus-circle"></i>
            {{__('messages.add_new')}}
        </a>
    </div>
    <x-filter/>
    <div class="row">
        <div class="col-12">
            <table class="table table-nowrap">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{__('messages.name')}}</th>
                    <th scope="col">{{__('messages.activation')}}</th>
                    <th scope="col">{{__('messages.actions')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($resources as $resource)
                    <tr id="role{{$resource->id}}Row">
                        <th scope="row">
                            <a href="#" class="fw-semibold">#{{$loop->iteration}}</a>
                        </th>
                        <td>{{$resource->name}}</td>
                        @include('dashboard.partials.__table-actions', ['resource' => $resource, 'route' => 'category-medical-equipments', 'showModel' => true])
                        @include('dashboard.category-medical-equipments.show', ['resource' => $resource])
                    </tr>
                @endforeach
                </tbody>
            </table>
            @include('dashboard.layouts.paginate')
        </div>
    </div>
@endsection
