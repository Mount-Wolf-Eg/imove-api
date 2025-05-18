@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.manage_consultation-questions')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.manage_consultation-questions')}}"
                  pagetitle="{{__('messages.consultation-questions')}}"
                  route="{{route('consultation-questions.index')}}"/>
    <x-filter>
        <div class="col-lg-2 py-1">
            {{ Form::label('Date', __('messages.date'), ['class' => 'form-label']) }}
            {!! Form::date('creationDate' , request('creationDate'), ['class' => 'form-control']) !!}
        </div>
        <div class="col-lg-2 py-1">
            {{ Form::label('doctor', __('messages.doctor'), ['class' => 'form-label']) }}
            {!! Form::select('doctor' , $doctors->pluck('user.name', 'id')->prepend(__('messages.select'), ''),
                request('doctor'),  ['class' => 'form-control select2']) !!}
        </div>
        <div class="col-lg-2 py-1">
            {{ Form::label('urgency_levels', __('messages.urgency_levels'), ['class' => 'form-label']) }}
            {!! Form::select('type' , $types->pluck('txt', 'value')->prepend(__('messages.select'), ''),
                request('type'),  ['class' => 'form-control select2']) !!}
        </div>
        @if(auth()->user()->vendor)
            <div class="col-lg-2 py-1">
                {{ Form::label('status', __('messages.status'), ['class' => 'form-label']) }}
                {!! Form::select('myVendorStatus' , $vendorStatuses->pluck('txt', 'value')->prepend(__('messages.select'), ''),
                    request('myVendorStatus'),  ['class' => 'form-control select2']) !!}
            </div>
        @endif
    </x-filter>
    <div class="row">
        <div class="col-12">
            <table class="table table-nowrap">
                <thead>
                <tr>
                     <th scope="col">{{__('messages.request_id')}}</th>
                    <th scope="col">{{__('messages.reporting_date')}}</th>
                    <th scope="col">{{__('messages.patient_name')}}</th>
                    <th scope="col">{{__('messages.doctor_name')}}</th>
                    <th scope="col">{{__('messages.medicalSpeciality')}}</th>
                    <th scope="col">{{__('messages.session_type')}}</th>
                    <th scope="col">{{__('messages.answers')}}</th>
                    <th scope="col">{{__('messages.actions')}}</th>

                </tr>
                </thead>
                <tbody>
                @foreach($resources as $resource)
                    <tr id="role{{$resource->id}}Row">
                
                        <td><a href="{{route('consultations.show', $resource->id)}}">#{{$resource->id}}</a></td>
                        <td>{{$resource->created_at->format('Y-m-d h:i A')}}</td>
                        <td>{{$resource->patient?->user?->name}}</td>
                        <td>{{$resource->doctor?->user?->name}}</td>
                        <td>{{$resource->medicalSpeciality?->name}}</td>
                        <td>{{$resource->type?->label()}}</td>
                        {{-- <td>{{ $resource->consultationQuestions }}</td> --}}
                        <td>{{ $resource->consultationQuestions->count() }}</td>

                        @include('dashboard.partials.__table-actions', ['resource' => $resource, 'disableEdit' => true,
                        'disableDelete' => true,
                        'route' => 'consultation-questions', 'hideActive' => true, 'showModel' => false])
       
                    </tr>
                @endforeach
                </tbody>
            </table>
            @include('dashboard.layouts.paginate')
        </div>
    </div>
@endsection
