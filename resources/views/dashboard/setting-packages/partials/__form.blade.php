{!! Form::open(['route' => $action, 'method'=> $method]) !!}
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <div class="row g-3">

                    <!-- Number of Sessions -->
                    <div class="col-lg-6">
                        {{ Form::label('num_of_sessions', __('messages.num_of_sessions'), ['class' => 'form-label']) }}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::number('num_of_sessions', old('num_of_sessions', $settingPackage->num_of_sessions ?? ''), ['class' => 'form-control', 'min' => 1, 'step' => 1]) !!}
                        @error("num_of_sessions")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <!-- Duration -->
                    <div class="col-lg-6">
                        {{ Form::label('duration', __('messages.duration'), ['class' => 'form-label']) }}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::number('duration', old('duration', $settingPackage->duration ?? ''), ['class' => 'form-control', 'min' => 1, 'step' => 1]) !!}
                        @error("duration")
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
