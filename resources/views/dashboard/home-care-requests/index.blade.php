@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.manage_home-care-requests')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.manage_home-care-requests')}}" pagetitle="{{__('messages.home-care-requests')}}" route="{{route('home-care-requests.index')}}"/>

    <x-filter>
        {{-- <div class="col-lg-2 py-1">
            {{ Form::label('Date', __('messages.date'), ['class' => 'form-label']) }}
            {!! Form::date('creationDate' , request('creationDate'), ['class' => 'form-control']) !!}
        </div> --}}
        {{-- <div class="col-lg-3">
            {{Form::label('medicalSpeciality', __('messages.medicalSpeciality'), ['class' => 'form-label'])}}
            {!! Form::select('medicalSpeciality', $medicalSpeciality->pluck('name', 'id')->prepend(__('messages.select'), ''),
                request('medicalSpeciality') ?? '',
                ['class' => 'form-select']) !!}
            @error("medicalSpeciality")
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>    --}}
    </x-filter>
    <div class="row">
        <div class="col-12">
            <table class="table table-nowrap">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{__('messages.patient')}}</th>
                    <th scope="col">{{__('messages.phone')}}</th>
                    <th scope="col">{{__('messages.modelSingle.city')}}</th>
                    {{-- <th scope="col">{{__('messages.medicalSpeciality')}}</th> --}}
                    <th scope="col">{{__('messages.created')}}</th>
                    <th scope="col">{{__('messages.status')}}</th>
                    <th scope="col">{{__('messages.actions')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($resources as $resource)
                    <tr id="homeCareRequest{{$resource->id}}Row">
                        <th scope="row">
                            <a href="#" class="fw-semibold">#{{$loop->iteration}}</a>
                        </th>
                        <td>{{$resource->patient?->user->name}}</td>
                        <td>{{$resource->patient?->user->phone}}</td>
                        <td>{{$resource->city?->name}}</td>
                        {{-- <td>{{$resource->medicalSpeciality?->name}}</td> --}}
                        <td>{{$resource->created_at?->format('Y-m-d')}}</td>
                        <td>
                            @if ($resource->status == 1)
                                @lang('messages.The request is being reviewed')
                            @elseif ($resource->status == 2)
                                @lang('messages.visited')
                            @else
                                @lang('messages.Reject')
                            @endif
                        </td>
                        @include('dashboard.partials.__table-actions', ['resource' => $resource, 'disableEdit' => true, 'route' => 'home-care-requests', 'showModel' => false, 'hideActive' => true])
                    </tr>
                @endforeach
                </tbody>
            </table>
            @include('dashboard.layouts.paginate')
        </div>
    </div>


@endsection
