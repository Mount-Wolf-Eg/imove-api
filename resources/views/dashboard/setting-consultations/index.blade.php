@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.setting-consultations')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.setting-consultations')}}" pagetitle="{{__('messages.setting-consultations')}}" route="{{route('setting-consultations.index')}}"/>
    @if($resources->count() < 1)
        <div class="d-flex justify-content-sm-end">
            <a href="{{route('setting-consultations.create')}}" class="btn btn-primary">
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
                    <th scope="col">{{__('messages.minimum')}}</th>
                    <th scope="col">{{__('messages.maximum')}}</th>
                    <th scope="col">{{__('messages.updated')}}</th>
                    <th scope="col">{{__('messages.actions')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($resources as $resource)
                    <tr id="faq{{$resource->id}}Row">
                        <th scope="row">
                            <a href="#" class="fw-semibold">#{{$loop->iteration}}</a>
                        </th>
                        <td>{{$resource->minimum}}</td>
                        <td>{{$resource->maximum}}</td>
                        <td>{{$resource->updated_at?->format('Y-m-d')}}</td>
                        @include('dashboard.partials.__table-actions', ['resource' => $resource, 'route' => 'setting-consultations', 'showModel' => true, 'hideActive' => true, 'disableDelete' => true])
                        @include('dashboard.setting-consultations.show', ['resource' => $resource])
                    </tr>
                @endforeach
                </tbody>
            </table>
            @include('dashboard.layouts.paginate')
        </div>
    </div>
@endsection
