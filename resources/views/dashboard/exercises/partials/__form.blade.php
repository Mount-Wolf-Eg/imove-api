{!! Form::open(['route' => $action, 'method'=> $method, 'enctype' => 'multipart/form-data']) !!}
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-lg-12">
                        {{Form::label('medicalSpecialities', __('messages.speciality'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::select('specialities[]' ,$specialities->pluck('name', 'id'),
                            isset($exercise) ? $exercise->medicalSpecialities->pluck('id') : [],
                            ['class' => 'form-select', 'multiple' => true]) !!}
                        @error("specialities")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                        @error("specialities.*")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="col-lg-6">
                        {{Form::label('name', __('messages.name_en'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::text('name[en]' , isset($exercise) ? $exercise->getTranslation('name', 'en') : '', ['class' => 'form-control']) !!}
                        @error("name.en")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('name', __('messages.name_ar'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::text('name[ar]' , isset($exercise) ? $exercise->getTranslation('name', 'ar') : '', ['class' => 'form-control']) !!}
                        @error("name.ar")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('brief', __('messages.brief_en'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::text('brief[en]' , isset($exercise) ? $exercise->getTranslation('brief', 'en') : '', ['class' => 'form-control']) !!}
                        @error("brief.en")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('brief', __('messages.brief_ar'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::text('brief[ar]' , isset($exercise) ? $exercise->getTranslation('brief', 'ar') : '', ['class' => 'form-control']) !!}
                        @error("brief.ar")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('description', __('messages.description_en'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::textarea('description[en]' , isset($exercise) ? $exercise->getTranslation('description', 'en') : '', ['class' => 'form-control']) !!}
                        @error("description.en")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('description', __('messages.description_ar'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::textarea('description[ar]' , isset($exercise) ? $exercise->getTranslation('description', 'ar') : '', ['class' => 'form-control']) !!}
                        @error("description.ar")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 {{app()->getLocale() == 'ar' ? 'ms-3' : 'me-3'}}">
                                        <div class="avatar-sm">
                                            <div class="avatar-title rounded-circle bg-light text-primary fs-20">
                                                <i class="bi bi-images"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1 d-inline">{{__('messages.video')}}</h5>
                                        @if(request()->routeIs('exercises.create'))
                                            <span class="text-danger fw-bold">*</span>
                                        @endif
                                        <p class="text-muted mb-0">{{__('messages.upload') . ' ' . __('messages.video')}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-2 pb-3">
                                {!! Form::file('media', ['class' => 'form-control', 'accept' => 'video/mp4, video/webm, video/ogg', 'value' => old('media')]) !!}
                                @error("media")
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                                <br>
                                @if(isset($exercise) && $exercise->media)
                                    <div class="col-6 position-relative">
                                        <a class="btn btn-flat-light my-3 mx-2 remove-image-resource position-absolute top-0 {{ app()->getLocale() == 'ar' ? 'start' : 'end' }}-0" data-id="{{ $exercise->media->id }}">
                                            <i class="bi bi-x-lg"></i>
                                        </a>

                                        <video controls class="mt-3" style="max-height: 200px; width: 100%;">
                                            <source src="{{ $exercise->media->asset_url }}" type="video/mp4">
                                            {{ __('messages.video_not_supported') ?? 'Your browser does not support the video tag.' }}
                                        </video>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 {{app()->getLocale() == 'ar' ? 'ms-3' : 'me-3'}}">
                                        <div class="avatar-sm">
                                            <div class="avatar-title rounded-circle bg-light text-primary fs-20">
                                                <i class="bi bi-images"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1 d-inline">{{__('messages.main_image')}}</h5>
                                        @if(request()->routeIs('exercises.create'))
                                            <span class="text-danger fw-bold">*</span>
                                        @endif
                                        <p class="text-muted mb-0">{{__('messages.upload') . ' ' . __('messages.main_image')}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-2 pb-3">
                                {!! Form::file('main_image', ['class' => 'form-control', 'accept' => 'image/jpeg, image/png', 'value' => old('main_image')]) !!}
                                @if(request()->routeIs('exercises.create'))
                                    <span class="text-danger fw-bold">*</span>
                                @endif
                                @error("main_image")
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                                <br>
                                @if(isset($exercise) && $exercise->mainImage)
                                    <div class="col-6 position-relative">
                                        <a class="btn btn-flat-light my-3 mx-2 remove-image-resource position-absolute top-0 {{app()->getLocale() == 'ar' ? 'start' : 'end'}}-0" data-id="{{$exercise->mainImage->id}}">
                                            <i class="bi bi-x-lg"></i>
                                        </a>
                                        <img src="{{$exercise->mainImage->asset_url}}" title="{{$exercise->mainImage->name}}" class="img-fluid mt-3" alt="{{__('messages.main_image')}}" style="max-height: 200px">
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
    <!--end col-->
</div>
{!! Form::close() !!}

