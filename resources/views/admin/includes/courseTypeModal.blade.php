{{-- modal begin --}}
<div class="modal fade" tabindex="-1" id="addNewCourse">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Course Type</h5>

                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <span class="svg-icon svg-icon-2x"></span>
                </div>
                <!--end::Close-->
            </div>
            {{-- {{ route('admin.saveLanguage') }} --}}
            <form method="post" action="{{ route('admin.addCourseType') }}">
                @csrf
                <div class="modal-body ">
                    <div class='form-group row mb-4'>
                        <label class=" col-lg-3 required form-label"> Course Name</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-solid" placeholder="Enter Course name"
                                name='course_name' required />
                        </div>
                    </div>
                    <div class='form-group row mb-5'>
                        <label class=" col-lg-3 required form-label">Type</label>
                        <div class="col-lg-9">
                            <select class="form-select form-select-solid" data-control="select2"
                                data-placeholder="Select an option" name='type' data-allow-clear="true" required
                                data-dropdown-parent="#addNewCourse">
                                <option value="UG">UG</option>
                                <option value="PG">PG</option>
                                <option value="MPHIL">Mphil</option>
                                <option value="PHD">PHD</option>
                                <option value="OTHER">OTHER</option>
                            </select>
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
