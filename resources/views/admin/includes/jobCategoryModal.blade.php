 {{-- modal for adding category --}}
 <div class="modal fade" tabindex="-1" id="addNewCategory">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Job Category</h5>

                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
                <!--end::Close-->
            </div>

            <form method="post" action="{{ route('admin.saveJobCategory') }}">
                @csrf
                <div class="modal-body ">
                    <div class='form-group row mb-4'>
                        <label class=" col-lg-3 required form-label">Job Category Name</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-solid"
                                placeholder="Enter a Job Category Name" name='name' required />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- modal end --}}
