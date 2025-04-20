@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.manage_static-pages')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.static-pages')}}" pagetitle="{{__('messages.static-pages')}}" route="{{route('static-pages.index')}}"/>
    
    {{-- <x-filter/> --}}
    <div class="row">
        <div class="col-12">
            <table class="table table-nowrap">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{__('messages.page')}}</th>
                    <th scope="col">{{__('messages.content')}}</th>
                    <th scope="col">{{__('messages.actions')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($resources as $resource)
                    <tr id="role{{$resource->id}}Row">
                        <th scope="row">
                            <a href="#" class="fw-semibold">#{{$loop->iteration}}</a>
                        </th>
                        <td>
                            @if($resource->page == 'terms_and_conditions_doctor')
                                @lang('messages.terms_and_conditions_doctor')
                            @elseif ($resource->page == 'terms_and_conditions_patient')
                                @lang('messages.terms_and_conditions_patient')
                            @else
                                {{ $resource->page }}
                            @endif
                        </td>
                        <td> {{ $resource->content }} </td>
                        {{-- <td>{!! $resource->content !!}</td> --}}
                        @include('dashboard.partials.__table-static-pages-actions', ['resource' => $resource, 'route' => 'static-pages', 'showModel' => true])
                        @include('dashboard.static-pages.show', ['resource' => $resource])
                    </tr>
                @endforeach
                </tbody>
            </table>
            @include('dashboard.layouts.paginate')
        </div>
    </div>
@endsection
