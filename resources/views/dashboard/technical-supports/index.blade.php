@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.manage_technical-support')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.manage_technical-support')}}"
                  pagetitle="{{__('messages.technical-support')}}"
                  route="{{route('technical-support.index')}}"/>
    <div class="d-flex justify-content-sm-end">
        {{-- <a href="{{route('technical-support.create')}}">
            <i class="bi bi-plus-circle"></i>
            {{__('messages.add_new')}}
        </a> --}}
    </div>
    <x-filter>
        {{-- <div class="col-lg-4">
            {{Form::label('type', __('messages.type'), ['class' => 'form-label'])}}
            {!! Form::select('category', $category->pluck('name', 'id')->prepend(__('messages.select'), ''),
                request('category') ?? '',
                ['class' => 'form-select']) !!}
            @error("category_id")
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div> --}}
    </x-filter>
    <div class="row">
        <div class="col-12">
            <table class="table table-nowrap">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{__('messages.type')}}</th>
                    <th scope="col">{{__('messages.name')}}</th>
                    
                    <th scope="col">{{__('messages.subject')}}</th>
                    <th scope="col">{{__('messages.email')}}</th>
                    <th scope="col">{{__('messages.message')}}</th>

                    <th scope="col">{{__('messages.actions')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($resources as $resource)
                    <tr id="role{{$resource->id}}Row">
                        <th scope="row">
                            <a href="#" class="fw-semibold">#{{$loop->iteration}}</a>
                        </th>
                        @if ($resource->doctor_id)
                            <td>@lang('messages.doctor')</td>
                            <td>{{$resource->doctor?->user->name}}</td>
                        @else
                            <td>@lang('messages.patient')</td>
                            <td>{{$resource->user?->name}}</td>
                        @endif

                        <td>{{$resource->topic}}</td>
                        <td>{{$resource->email}}</td>
                        <td>{{$resource->message}}</td>
                        @include('dashboard.partials.__table-technical-support-actions', ['resource' => $resource, 'route' => 'technical-support', 'showModel' => false])
                    </tr>
                @endforeach
                </tbody>
            </table>
            @include('dashboard.layouts.paginate')
        </div>
    </div>
@endsection
