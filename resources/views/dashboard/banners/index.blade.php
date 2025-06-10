@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.manage_banners')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.manage_banners')}}" pagetitle="{{__('messages.banners')}}" route="{{route('banners.index')}}"/>
    <div class="d-flex justify-content-sm-end">
        <a href="{{route('banners.create')}}">
            <i class="bi bi-plus-circle"></i>
            {{__('messages.add_new')}}
        </a>
    </div>

    <div class="row">
        <div class="col-12">
            <table class="table table-nowrap">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{__('messages.image')}}</th>
                    <th scope="col">{{__('messages.actions')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($resources as $resource)
                    <tr id="exercis{{$resource->id}}Row">
                        <th scope="row">
                            <a href="#" class="fw-semibold">#{{$loop->iteration}}</a>
                        </th>
                        <td>
                            <img src="{{ $resource->mainImage->asset_url ?? asset('assets/images/logo-sm.png') }}" alt="" class="rounded avatar-md">
                        </td>

                        @include('dashboard.partials.__table-actions', ['resource' => $resource, 'route' => 'banners', 'hideActive' => true, 'showModel' => false])
                    </tr>
                @endforeach
                </tbody>
            </table>
            @include('dashboard.layouts.paginate')
        </div>
    </div>


@endsection
