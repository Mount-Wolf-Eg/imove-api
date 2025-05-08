{!! Form::open(['route' => $action, 'method'=> $method, 'enctype' => 'multipart/form-data']) !!}
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-12">
                        {{Form::label('name', __('messages.name'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::text('name', $package->name ?? '', ['class' => 'form-control']) !!}
                        @error("name")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-12">
                        {{Form::label('description', __('messages.description'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::textarea('description', $package->description ?? '', ['class' => 'form-control']) !!}
                        @error("description")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('num_of_sessions', __('messages.num_of_sessions'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::number('num_of_sessions', $package->num_of_sessions ?? '', ['class' => 'form-control', 'min' => 1]) !!}
                        @error("num_of_sessions")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('duration', __('messages.duration'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::text('duration', $package->duration ?? '', ['class' => 'form-control']) !!}
                        @error("duration")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('price', __('messages.price'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::number('price', $package->price ?? '', ['class' => 'form-control', 'step' => '0.01', 'min' => 0]) !!}
                        @error("price")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('doctor', __('messages.doctor_name'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::select('user_id', $doctors->pluck('name', 'id')->prepend(__('messages.select'), ''), $package->user_id ?? '', ['class' => 'form-select']) !!}
                        @error("user_id")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-12">
                        {{Form::label('is_active', __('messages.is_active'), ['class' => 'form-label'])}}
                        <div class="form-check form-switch">
                            {!! Form::checkbox('is_active', 1, $package->is_active ?? false, ['class' => 'form-check-input']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">{{__('messages.image')}}</h5>
                                <p class="text-muted">{{__('messages.upload') . ' ' . __('messages.image')}}</p>
                            </div>
                            <div class="card-body">
                                {!! Form::file('image', ['class' => 'form-control', 'accept' => 'image/jpeg, image/png']) !!}
                                @error("image")
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                                @if(isset($package) && $package->mainImage)
                                    <div class="col-6 position-relative mt-3">
                                        <a class="btn btn-flat-light remove-image-resource position-absolute top-0 {{app()->getLocale() == 'ar' ? 'start' : 'end'}}-0" data-id="{{$package->mainImage->id}}">
                                            <i class="bi bi-x-lg"></i>
                                        </a>
                                        <img src="{{$package->mainImage->asset_url}}" title="{{$package->mainImage->name}}" class="img-fluid" alt="{{__('messages.image')}}" style="max-height: 200px">
                                    </div>
                                @endif
                            </div>
                        </div>
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
</div>
{!! Form::close() !!}
<form class="d-inline" method="POST" id="removeImageForm">
    @csrf
    @method('DELETE')
</form>
