@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.home-care-requests')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.home-care-requests_details')}}" pagetitle="{{__('messages.home-care-requests')}}" route="{{route('home-care-requests.index')}}"/>
    <div class="row">
        <div class="col-md-12">
            <div class="card p-2">
                <div class="card-body">

                    <h5 class="card-title py-2">
                        @lang('messages.status')  &nbsp; &nbsp;: &nbsp; &nbsp;
                        @if ($homeCareRequest->status == 1)
                            {{ucfirst( __('messages.The request is being reviewed'))}}
                        @elseif ($homeCareRequest->status == 2)
                            {{ucfirst( __('messages.visited'))}}
                        @else
                            {{ucfirst( __('messages.Reject'))}}
                        @endif
                    </h5>
                    <p class="card-text">{{__('messages.created')}}: {{$homeCareRequest->created_at?->format('Y-m-d')}}</p>
                    <h5 class="card-title py-2">{{__('messages.details')}}</h5>
                    <div class="row py-2">
                        <div class="col-2">{{__('messages.patient')}}</div>
                        <div class="col-10">{{$homeCareRequest->patient?->user->name}}</div>
                    </div>
                    <div class="row py-2">
                        <div class="col-2">{{__('messages.phone')}}</div>
                        <div class="col-10">{{$homeCareRequest->patient?->user->phone}}</div>
                    </div>
                    {{-- <div class="row py-2">
                        <div class="col-2">{{__('messages.speciality')}}</div>
                        <div class="col-10">{{$homeCareRequest->medicalSpeciality?->name}}</div>
                    </div> --}}
                    <div class="row py-2">
                        <div class="col-2">{{__('messages.modelSingle.city')}}</div>
                        <div class="col-10">{{$homeCareRequest->city?->name}}</div>
                    </div>
                    <div class="row py-2">
                        <div class="col-2">{{__('messages.address')}}</div>
                        <div class="col-10">{{$homeCareRequest->address}}</div>
                    </div>
                    <div class="row py-2">
                        <div class="col-2">{{__('messages.description')}}</div>
                        <div class="col-10">{{$homeCareRequest->description}}</div>
                    </div>
                    <div class="row py-2">
                        <div class="col-2">{{__('messages.updated')}}</div>
                        <div class="col-10">{{$homeCareRequest->updated_at ? $homeCareRequest->updated_at?->format('Y-m-d') : __('messages.not_published')}}</div>
                    </div>

                    <div class="row py-2">
                        <div class="col-12">
                            {{-- @if ($homeCareRequest->status == 1) --}}
                                <form action="{{ route('home-care-requests.update-status', $homeCareRequest->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="3">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('messages.confirm_reject') }}')">
                                        {{ __('messages.Reject') }}
                                    </button>
                                </form>
                                <form action="{{ route('home-care-requests.update-status', $homeCareRequest->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="2">
                                    <button type="submit" class="btn btn-success" onclick="return confirm('{{ __('messages.confirm_visited') }}')">
                                        {{ __('messages.visited') }}
                                    </button>
                                </form>
                            {{-- @endif --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
