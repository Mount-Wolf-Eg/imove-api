
<td>

    @if(!isset($disableEdit) || !$disableEdit)
        <a href="{{route("$route.edit", $resource->id)}}" class="link-info px-2">
            {{__('messages.edit')}} <i class="bi bi-pencil-fill"></i>
        </a>
    @endif

</td>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.delete-resource').on('click', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                Swal.fire({
                    title: '{{__('messages.confirm.are_you_sure')}}',
                    text: '{{__('messages.confirm.delete_resource')}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2a4fd7',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{__('messages.confirm.yes_delete')}}',
                    cancelButtonText: '{{__('messages.confirm.cancel')}}',
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        $('#deleteResourceForm-'+id).submit();
                    }
                })
            })
            $('.active-resource').on('change', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                let activation = $(this).data('activation');
                Swal.fire({
                    title: '{{__('messages.confirm.are_you_sure')}}',
                    text: '{{__('messages.confirm.change_resource_activation')}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2a4fd7',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{__('messages.confirm.yes_change')}}',
                    cancelButtonText: '{{__('messages.confirm.cancel')}}',
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        $('#activeResourceForm-'+id).submit();
                    } else {
                        if (activation === 1) {
                            $(this).prop('checked', true);
                        } else {
                            $(this).prop('checked', false);
                        }
                    }
                })
            })
        });
        $(document).ready(function() {
            $('#resource-details{{$resource->id}}').on('click', function(e) {
                e.preventDefault();
                $('#detailsModal{{$resource->id}}').modal('show');
            });
        });
    </script>
@endpush
