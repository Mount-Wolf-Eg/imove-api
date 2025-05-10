@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.setting-packages')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.setting-packages')}}" pagetitle="{{__('messages.setting-packages')}}" route="{{route('setting-packages.index')}}"/>
    @if($resources->count() < 1)
        <div class="d-flex justify-content-sm-end">
            <a href="{{route('setting-packages.create')}}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i>
                {{__('messages.add_new')}}
            </a>
        </div>
    @endif
    <x-filter/>
    <div class="row">
        <div class="col-12">
            <table class="table table-nowrap">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{__('messages.num_of_sessions')}}</th>
                    <th scope="col">{{__('messages.duration')}}</th>
                    <th scope="col">{{__('messages.creation_date')}}</th>
                    <!-- <th scope="col">{{__('messages.activation')}}</th> -->
                    <th scope="col">{{__('messages.actions')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($resources as $resource)
                    <tr id="faq{{$resource->id}}Row">
                        <th scope="row">
                            <a href="#" class="fw-semibold">#{{$loop->iteration}}</a>
                        </th>
                        <td>{{$resource->num_of_sessions}}</td>
                        <td>{{$resource->duration}}</td>
                        <td>{{$resource->created_at?->format('Y-m-d')}}</td>
                        @include('dashboard.partials.__table-actions', ['resource' => $resource, 'route' => 'setting-packages', 'showModel' => true, 'hideActive' => true])
                        @include('dashboard.setting-packages.show', ['resource' => $resource])
                    </tr>
                @endforeach
                </tbody>
            </table>
            @include('dashboard.layouts.paginate')
        </div>
    </div>
@endsection
