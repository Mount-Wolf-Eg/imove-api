{!! Form::open(['route' => $action, 'method'=> $method]) !!}
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-lg-6">
                        {{ Form::label('minimum', __('messages.minimum'), ['class' => 'form-label']) }}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::number('minimum', old('minimum', $settingConsultation->minimum ?? ''), ['class' => 'form-control', 'min' => 1, 'step' => 1.0]) !!}
                        @error("minimum")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="col-lg-6">
                        {{ Form::label('maximum', __('messages.maximum'), ['class' => 'form-label']) }}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::number('maximum', old('maximum', $settingConsultation->maximum ?? ''), ['class' => 'form-control', 'min' => 1, 'step' => 1.0]) !!}
                        @error("maximum")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="col-lg-12">
                        <div class="{{app()->getLocale() == 'ar' ? 'text-start' : 'text-end'}}">
                            <button type="submit" class="btn btn-primary">{{__('messages.save')}}</button>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
</div>
{!! Form::close() !!}
