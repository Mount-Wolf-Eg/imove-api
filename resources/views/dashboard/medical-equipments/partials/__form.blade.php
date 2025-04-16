{!! Form::open(['route' => $action, 'method'=> $method, 'enctype' => 'multipart/form-data']) !!}
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-lg-6">
                        {{Form::label('name', __('messages.name_en'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::text('name[en]' , isset($medicalEquipment) ? $medicalEquipment->getTranslation('name', 'en') : '', ['class' => 'form-control']) !!}
                        @error("name.en")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('name', __('messages.name_ar'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::text('name[ar]' , isset($medicalEquipment) ? $medicalEquipment->getTranslation('name', 'ar') : '', ['class' => 'form-control']) !!}
                        @error("name.ar")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="col-lg-6">
                        {{Form::label('name', __('messages.link'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::text('link' , $medicalEquipment->link ?? '', ['class' => 'form-control']) !!}
                        @error("link")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('type', __('messages.type'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::select('category_id', $category->pluck('name', 'id')->prepend(__('messages.select'), ''),
                            $medicalEquipment->category_id ?? '',
                            ['class' => 'form-select']) !!}
                        @error("category_id")
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
                                        <h5 class="card-title mb-1 d-inline">{{__('messages.image')}}</h5>
                                        @if(request()->routeIs('medical-equipments.create'))
                                            <span class="text-danger fw-bold">*</span>
                                        @endif
                                        <p class="text-muted mb-0">{{__('messages.upload') . ' ' . __('messages.image')}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-2 pb-3">
                                {!! Form::file('photo', ['class' => 'form-control', 'accept' => 'image/jpeg, image/png', 'value' => old('photo')]) !!}
                                @error("photo")
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                                <br>
                                @if(isset($medicalEquipment) && $medicalEquipment->photo)
                                    <div class="col-6 position-relative">
                                        <a class="btn btn-flat-light my-3 mx-2 remove-image-resource position-absolute top-0 {{app()->getLocale() == 'ar' ? 'start' : 'end'}}-0" data-id="{{$medicalEquipment->photo->id}}">
                                            <i class="bi bi-x-lg"></i>
                                        </a>
                                        <img src="{{$medicalEquipment->photo->asset_url}}" title="{{$medicalEquipment->photo->name}}" class="img-fluid mt-3" alt="{{__('messages.photo')}}" style="max-height: 200px">
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
