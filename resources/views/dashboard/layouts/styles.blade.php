<script src="{{ URL::asset('assets/js/layout.js') }}"></script>
<link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ URL::asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ URL::asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@if(app()->getLocale() == 'ar')
    <link href="{{ URL::asset('assets/css/app-rtl.min.css') }}" rel="stylesheet" type="text/css">
@else
    <link href="{{ URL::asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css">
@endif
@stack('styles')
