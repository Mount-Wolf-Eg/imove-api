@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.consultation-questions')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.consultation-questions_details')}}" pagetitle="{{__('messages.consultation-questions')}}" route="{{route('consultation-questions.index')}}"/>
    <div class="row">
        <div class="col-lg-6">
            @include('dashboard.consultation-questions.partials.__patient-info')
        </div>
        <div class="col-lg-6 text-right">
            @include('dashboard.consultation-questions.partials.__doctor-info')
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card p-2">
                <div class="card-body">
                    <h2 class="card-title py-2">{{__('messages.answers') . '&' . __('messages.questions')}}</h2>
        
                    @foreach($consultation->consultationQuestions as $consultationQuestion)
                        <div class="row py-2">
                            <div class="col-12">{{$loop->iteration}} -  {{$consultationQuestion->question}}</div>
                            <div class="col-12">- {{$consultationQuestion->pivot->answer?? '------'}}</div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

    </div>


@endsection
