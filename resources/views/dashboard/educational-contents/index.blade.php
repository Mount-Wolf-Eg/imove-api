@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.manage_educational-contents')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.manage_educational-contents')}}" pagetitle="{{__('messages.educational-contents')}}" route="{{route('educational-contents.index')}}"/>
    <div class="d-flex justify-content-sm-end">
        <a href="{{route('educational-contents.create')}}">
            <i class="bi bi-plus-circle"></i>
            {{__('messages.add_new')}}
        </a>
    </div>
    <x-filter>
        <div class="col-lg-4">
            {{Form::label('medicalSpeciality', __('messages.medicalSpeciality'), ['class' => 'form-label'])}}
            {!! Form::select('medicalSpeciality', $medicalSpeciality->pluck('name', 'id')->prepend(__('messages.select'), ''), 
                request('medicalSpeciality') ?? '', 
                ['class' => 'form-select']) !!}
            @error("medicalSpeciality")
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
                    <th scope="col">{{__('messages.title')}}</th>
                    {{-- <th scope="col">{{__('messages.content')}}</th> --}} 
                    <th scope="col">{{__('messages.medicalSpeciality')}}</th> 
                    <th scope="col">{{__('messages.author')}}</th>
                    <th scope="col">{{__('messages.publish_date')}}</th>
                    <th scope="col">{{__('messages.likes')}}</th>
                    <th scope="col">{{__('messages.views')}}</th>
                    <th scope="col">{{__('messages.activation')}}</th>
                    <th scope="col">{{__('messages.actions')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($resources as $resource)
                    <tr id="educationalContent{{$resource->id}}Row">
                        <th scope="row">
                            <a href="#" class="fw-semibold">#{{$loop->iteration}}</a>
                        </th>
                        <td>{{$resource->title}}</td> 
                        <td>{{$resource->medicalSpeciality?->name}}</td>
                        <td>{{$resource->author?->name}}</td>
                        <td>{{$resource->publish_date ? $resource->publish_date?->format('Y-m-d') : __('messages.not_published')}}</td>
                        <td>{{count($resource->likes)}}</td>
                        <td>{{$resource->views}}</td>
                        @include('dashboard.partials.__table-actions', ['resource' => $resource, 'route' => 'educational-contents', 'showModel' => false])
                    </tr>
                @endforeach
                </tbody>
            </table>
            @include('dashboard.layouts.paginate')
        </div>
    </div>

  
@endsection
