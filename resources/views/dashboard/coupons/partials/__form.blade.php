{!! Form::open(['route' => $action, 'method'=> $method]) !!}
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        {{Form::label('code', __('messages.code'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::text('code' , $coupon->code ?? '', ['class' => 'form-control']) !!}
                        @error("code")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('type', __('messages.discount_type'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::select('discount_type', $types,
                            isset($coupon) ? [$coupon->discount_type->value] : [],
                            ['id' => 'discount_type', 'class' => 'form-select', 'onchange' => 'setDiscountValueAttributes()']) !!}
                        @error("discount_type")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('discount_amount', __('messages.discount_amount'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::number('discount_amount' , $coupon->discount_amount ?? '', ['class' => 'form-control', 'min' => '1', 'pattern' => '[0-9]', 'onkeypress' => 'return isNumberKey(event)']) !!}
                        @error("discount_amount")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('date', __('messages.valid_from'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::date('valid_from' , $coupon->valid_from ?? '', ['class' => 'form-control']) !!}
                        @error("valid_from")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('date', __('messages.valid_to'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::date('valid_to' , $coupon->valid_to ?? '', ['class' => 'form-control']) !!}
                        @error("valid_to")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('description', __('messages.description'), ['class' => 'form-label'])}}
                        {!! Form::textarea('description' , $coupon->description ?? '', ['class' => 'form-control']) !!}
                        @error("description.en")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('specialities', __('messages.specialities'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::select('specialities[]' ,$specialities->pluck('name', 'id'),
                            isset($coupon) ? $coupon->medicalSpecialities->pluck('id') : [],
                            ['class' => 'form-select', 'multiple' => true]) !!}
                        @error("specialities")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                        @error("specialities.*")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('user_limit', __('messages.user_limit'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::number('user_limit' , $coupon->user_limit ?? '', ['class' => 'form-control', 'pattern' => '[0-9]', 'onkeypress' => 'return isNumberKey(event)']) !!}
                        @error("user_limit")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="col-lg-6">
                        {{Form::label('total_limit', __('messages.total_limit'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::number('total_limit' , $coupon->total_limit ?? '', ['class' => 'form-control', 'pattern' => '[0-9]', 'onkeypress' => 'return isNumberKey(event)']) !!}
                        @error("total_limit")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('patients', __('messages.patients'), ['class' => 'form-label'])}}
                        {{-- <span class="text-danger fw-bold">*</span> --}}
                        {!! Form::select('users[]' ,$patients->pluck('user.phone', 'user_id'),
                            isset($coupon) ? $coupon->users->pluck('id') : [],
                            ['class' => 'form-select', 'multiple' => true]) !!}
                        @error("users")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                        @error("users.*")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('cities', __('messages.cities'), ['class' => 'form-label'])}}
                        {{-- <span class="text-danger fw-bold">*</span> --}}
                        {!! Form::select('cities[]' ,$cities->pluck('name', 'id'),
                            isset($coupon) ? $coupon->cities->pluck('id') : [],
                            ['class' => 'form-select', 'multiple' => true]) !!}
                        @error("cities")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                        @error("cities.*")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="col-lg-12">
                        <h5>@lang('messages.The coupon can be used with') : -</h5>
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('package', __('messages.package'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::checkbox('package' , $coupon->package ?? false, ['class' => 'form-control']) !!}
                        @error("package")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="col-lg-6">
                        {{Form::label('consultation', __('messages.consultation'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::checkbox('consultation' , $coupon->consultation ?? false, ['class' => 'form-control']) !!}
                        @error("consultation")
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

@push('scripts')
    <script>
        $(document).ready(function() {
            const discountTypeInput = $('#discount_type');
            const discountAmountInput = $('#discount_amount');

            if (discountTypeInput.val() === '1') {
                discountAmountInput.attr('max', '100');
            } else {
                discountAmountInput.removeAttr('max');
            }
        });
    </script>
    <script>
        $(document).ready(function () {
            function enforceExclusiveSelection() {
                const usersSelected = $('select[name="users[]"]').val().filter(Boolean).length > 0;
                const citiesSelected = $('select[name="cities[]"]').val().filter(Boolean).length > 0;

                if (usersSelected && citiesSelected) {
                    alert("من فضلك اختر المرضى أو المدن فقط، وليس الإثنين معًا.");
                    $('select[name="cities[]"]').val(null).trigger('change');
                }
            }

            $('select[name="users[]"], select[name="cities[]"]').on('change', enforceExclusiveSelection);
        });
    </script>
@endpush
