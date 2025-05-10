@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.manage_medicalEquipments')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.manage_medicalEquipments')}}"
                  pagetitle="{{__('messages.medicalEquipments')}}"
                  route="{{route('medical-equipments.index')}}"/>
    <div class="d-flex justify-content-sm-end">
        <a href="{{route('medical-equipments.create')}}">
            <i class="bi bi-plus-circle"></i>
            {{__('messages.add_new')}}
        </a>
    </div>
    <x-filter>
        <div class="col-lg-4">
            {{Form::label('type', __('messages.type'), ['class' => 'form-label'])}}
            {!! Form::select('category', $category->pluck('name', 'id')->prepend(__('messages.select'), ''),
                request('category') ?? '',
                ['class' => 'form-select']) !!}
            @error("category_id")
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
    </x-filter>
    <div class="row">
        <div class="col-12">
            <table class="table table-nowrap">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{__('messages.name')}}</th>
                    <th scope="col">{{__('messages.type')}}</th>
                    <th scope="col">{{__('messages.link')}}</th>
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
                        <td>{{$resource->category->name}}</td>
                        <td>{{$resource->link}}</td>
                        @include('dashboard.partials.__table-actions', ['resource' => $resource, 'route' => 'medical-equipments', 'showModel' => false])
                    </tr>
                @endforeach
                </tbody>
            </table>
            @include('dashboard.layouts.paginate')
        </div>
    </div>
@endsection
