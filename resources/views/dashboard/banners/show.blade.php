@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.banners')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.details')}}" pagetitle="{{__('messages.banners')}}" route="{{route('banners.index')}}"/>
    <div class="row">
        <div class="col-md-10">
            <div class="card">
                @if($banner->media && Str::endsWith($banner->media->asset_url, ['.mp4', '.webm', '.ogg']))
                    <video controls style="max-height:400px; width: 100%;" class="mx-auto d-block float-md-left mr-md-4">
                        <source src="{{ $banner->media->asset_url }}" type="video/mp4">
                         @lang('messages.The browser does not support video playback')
                    </video>
                @else
                    <img src="{{ $banner->media->asset_url ?? asset('assets/images/logo-sm.png') }}" class="card-img-top img-fluid mx-auto d-block float-md-left mr-md-4" style="max-height:400px;">
                @endif
            </div>
            
           
        </div>

    </div>

@endsection
