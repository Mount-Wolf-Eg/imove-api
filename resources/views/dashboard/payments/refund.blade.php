@extends('dashboard.layouts.master')
@section('title')
{{__('messages.manage_refund_requests')}}
@endsection
@section('content')
<x-breadcrumb title="{{__('messages.manage_refund_requests')}}" pagetitle="{{__('messages.refunds')}}" route="{{route('refund-request')}}" />

<!-- Filter Section -->
<x-filter>
    <div class="row">
        <div class="col-lg-4 py-2">
            {{ Form::label('status', __('messages.status'), ['class' => 'form-label']) }}
            <div class="input-group">
                {!! Form::select('status', $statuses->pluck('txt', 'value')->prepend(__('messages.select'), ''), request('status'), ['class' => 'form-control select2 form-select form-select-lg w-100']) !!}
            </div>
        </div>
        <div class="col-lg-4 py-2">
            {{ Form::label('fromCreationDate', __('messages.from'), ['class' => 'form-label']) }}
            <div class="input-group">
                {!! Form::date('fromCreationDate', request('fromCreationDate'), ['class' => 'form-control w-100']) !!}
            </div>
        </div>
        <div class="col-lg-4 py-2">
            {{ Form::label('toCreationDate', __('messages.to'), ['class' => 'form-label']) }}
            <div class="input-group">
                {!! Form::date('toCreationDate', request('toCreationDate'), ['class' => 'form-control w-100']) !!}
            </div>
        </div>
    </div>
</x-filter>

<!-- Refund Requests Table -->
<div class="row">
    <div class="col-12">
        <table class="table table-nowrap">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{__('messages.date')}}</th>
                    <th>{{__('messages.amount')}}</th>
                    <th>{{__('messages.name')}}</th>
                    <th>{{__('messages.national_id')}}</th>
                    <th>{{__('messages.iban_number')}}</th>
                    <th>{{__('messages.status')}}</th>
                    <th>{{__('messages.actions')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resources as $resource)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $resource->created_at->format('Y-m-d h:i A') }}</td>
                    <td>{{ $resource->amount }}</td>
                    <td>{{ $resource->beneficiary?->name }}</td>
                    <td>{{ $resource->beneficiary?->patient?->national_id ?: $resource->beneficiary?->doctor?->national_id }}</td>
                    <td>{{ $resource->beneficiary?->bank?->iban }}</td>
                    <td><span class="text-{{$resource->status->colorClass()}}">{{$resource->status->label()}}</span></td>
                    <td>
                        <form action="{{ route('refund-request.accept', $resource->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success btn-sm">{{__('messages.accept')}}</button>
                        </form>
                        <form action="{{ route('refund-request.reject', $resource->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-danger btn-sm">{{__('messages.reject')}}</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @include('dashboard.layouts.paginate')
    </div>
</div>
@endsection