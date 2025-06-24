@extends('dashboard.layouts.master')
@section('title')
    {{__('messages.manage_patients')}}
@endsection

@section('content')
    <x-breadcrumb title="{{__('messages.manage_patients')}}" pagetitle="{{__('messages.patients')}}" route="{{route('patients.index')}}" />

    <div class="d-flex justify-content-sm-end mb-2">
        <a href="{{route('patients.create')}}">
            <i class="bi bi-plus-circle"></i>
            {{__('messages.add_new')}}
        </a>
    </div>

    <x-filter />

    <form id="bulk-delete-form" method="POST" action="{{ route('patients.bulk-delete') }}">
        @csrf
        @method('DELETE')

        <button type="submit" class="btn btn-danger mb-3" id="bulkDeleteBtn" disabled>
            <i class="bi bi-trash"></i> {{ __('messages.delete_selected') }}
        </button>

        <table class="table table-nowrap">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>#</th>
                    <th>{{__('messages.name')}}</th>
                    <th>{{__('messages.national_id')}}</th>
                    <th>{{__('messages.phone')}}</th>
                    <th>{{__('messages.activation')}}</th>
                    <th>{{__('messages.actions')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resources as $resource)
                    <tr>
                        <td><input type="checkbox" class="patient-checkbox" name="ids[]" value="{{ $resource->id }}"></td>
                        <th scope="row">#{{ $loop->iteration }}</th>
                        <td>{{ $resource->user?->name }}</td>
                        <td>{{ $resource->national_id }}</td>
                        <td>{{ $resource->user?->phone }}</td>
                        @include('dashboard.partials.__table-actions', ['resource' => $resource, 'route' => 'patients', 'showModel' => false])
                    </tr>
                @endforeach
            </tbody>
        </table>
    </form>

    @include('dashboard.layouts.paginate')
@endsection

@push('scripts')
<script>
    // Toggle all checkboxes
    document.getElementById('checkAll').addEventListener('change', function () {
        const isChecked = this.checked;
        document.querySelectorAll('.patient-checkbox').forEach(cb => cb.checked = isChecked);
        toggleDeleteBtn();
    });

    document.querySelectorAll('.patient-checkbox').forEach(cb => {
        cb.addEventListener('change', toggleDeleteBtn);
    });

    function toggleDeleteBtn() {
        const checked = document.querySelectorAll('.patient-checkbox:checked').length > 0;
        document.getElementById('bulkDeleteBtn').disabled = !checked;
    }

    // Confirm before bulk delete
    document.getElementById('bulk-delete-form').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: '{{__("messages.confirm.are_you_sure")}}',
            text: '{{__("messages.confirm_delete_selected")}}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2a4fd7',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{__("messages.confirm.yes_delete")}}',
            cancelButtonText: '{{__("messages.confirm.cancel")}}',
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
    });
</script>
@endpush
