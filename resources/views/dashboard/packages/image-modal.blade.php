<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">{{$package->name}}'s Image</h5>
                <button type="button" class="btn btn-flat-light close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="bi bi-x-lg"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <img src="{{$package->image->url ?? asset('assets/images/logo-sm.png')}}" alt="Package Image" id="modalImage" class="img-fluid rounded g-modal-image w-auto">
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.gallery-image').click(function () {
                const imageUrl = $(this).attr('src');
                $('#modalImage').attr('src', imageUrl);
                $('#imageModal').modal('show');
            });
        });
    </script>
@endpush
