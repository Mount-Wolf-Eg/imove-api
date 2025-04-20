{!! Form::open(['route' => $action, 'method'=> $method]) !!}
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    
                    <div class="col-lg-12">
                    @if(isset($staticPage))
                        <h3>
                        @if($staticPage->page == 'terms_and_conditions_doctor')
                            @lang('messages.terms_and_conditions_doctor')
                        @elseif ($staticPage->page == 'terms_and_conditions_patient')
                            @lang('messages.terms_and_conditions_patient')
                        @else
                            {{ $staticPage->page }}
                        @endif 
                        </h3>
                    @endif
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('content', __('messages.content_en'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::textarea('content[en]' , isset($staticPage) ? $staticPage->getTranslation('content', 'en') : '', ['class' => 'form-control', 'required' => 'required', 'id' => 'note', 'maxlength' => 500]) !!}
                        {{-- {!! Form::textarea('content[en]' , isset($staticPage) ? $staticPage->getTranslation('content', 'en') : '', ['class' => 'form-control ckeditor-classic', 'required' => 'required', 'id' => 'note', 'maxlength' => 500]) !!} --}}
                        @error("content.en")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="col-lg-6">
                        {{Form::label('content', __('messages.content_ar'), ['class' => 'form-label'])}}
                        <span class="text-danger fw-bold">*</span>
                        {!! Form::textarea('content[ar]' , isset($staticPage) ? $staticPage->getTranslation('content', 'ar') : '', ['class' => 'form-control', 'required' => 'required', 'maxlength' => 500]) !!}
                        @error("content.ar")
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
    <!-- ckeditor -->
    <script src="{{ URL::asset('assets/libs/@ckeditor/ckeditor5-build-classic/ckeditor.js') }}"></script>
		<script src="{{ URL::asset('assets/js/pages/form-editor.init.js') }}"></script>

    <script>
        $(document).ready(function() {
            const textarea = $("#note");
            const charCount = $("#noteTextCounter");
            let currentLength = textarea.val().length;
            charCount.text(`${currentLength}/500`);
            textarea.on("input", () => {
                currentLength = textarea.val().length;
                charCount.text(`${currentLength}/500`);
            });
            $(".close").on("click", function(e) {
                textarea.val("");
                charCount.text("0/500");
            });
        });
    </script>
@endpush
