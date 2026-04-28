{{-- Confirm Delete Modal --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Confirm Deletion
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this record? This action <strong>cannot be undone</strong>.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, url) {
    document.getElementById('deleteForm').action = url;
    $('#confirmDeleteModal').modal('show');
}
</script>
