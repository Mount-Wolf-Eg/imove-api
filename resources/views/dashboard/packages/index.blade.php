@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.manage_packages')}}
@endsection
@section('content')
    <x-breadcrumb title="{{__('messages.manage_packages')}}" pagetitle="{{__('messages.packages')}}" route="{{route('packages.index')}}"/>
    <div class="d-flex justify-content-sm-end">
        <a href="{{route('packages.create')}}">
            <i class="bi bi-plus-circle"></i>
            {{__('messages.add_new')}}
        </a>
    </div>
    <x-filter/>
    <div class="row">
        <div class="col-12">
            <table class="table table-nowrap">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{__('messages.name')}}</th>
                    <th scope="col">{{__('messages.description')}}</th>
                    <th scope="col">{{__('messages.num_of_sessions')}}</th>
                    <th scope="col">{{__('messages.duration')}}</th>
                    <th scope="col">{{__('messages.price')}}</th>
                    <th scope="col">{{__('messages.is_active')}}</th>
                    <th scope="col">{{__('messages.doctor_name')}}</th>
                    <th scope="col">{{__('messages.actions')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($resources as $resource)
                    <tr id="course{{$resource->id}}Row">
                        <th scope="row">
                            <a href="#" class="fw-semibold">#{{$loop->iteration}}</a>
                        </th>
                        <td>{{$resource->name}}</td>
                        <td>{{$resource->description}}</td>
                        <td>{{$resource->num_of_sessions}}</td>
                        <td>{{$resource->duration}}</td>
                        <td>{{$resource->price}}</td>
                        <td>
                            <div class="form-check form-switch d-inline">
                                <input class="form-check-input activate-resource" type="checkbox" data-id="{{$resource->id}}" data-activation="{{$resource->is_active ? 1 : 0}}"
                                    @checked($resource->is_active)>
                            </div>
                            <form action="{{route('packages.active', $resource->id)}}" method="POST" id="activateResourceForm-{{$resource->id}}">
                                @csrf
                                @method('PUT')
                            </form>
                        </td>
                        <td>{{$resource->user?->name}}</td>
                        @include('dashboard.partials.__table-actions', ['resource' => $resource, 'route' => 'packages', 'showModel' => false, 'hideActive' => true])
                    </tr>
                @endforeach
                </tbody>
            </table>
            @include('dashboard.layouts.paginate')
        </div>
    </div>

    @push('scripts')
        <script>
            $('.activate-resource').on('change', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                let activation = $(this).data('activation');
                let text = activation ? 'You want to deactivate this Course!' : 'You want to activate this Course!';
                Swal.fire({
                    title: 'Are you sure?',
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2a4fd7',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        $('#activateResourceForm-'+id).submit();
                    } else {
                        $(this).prop('checked', activation);
                    }
                })
            })
        </script>
    @endpush
@endsection
