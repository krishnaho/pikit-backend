@extends('admin.layout.master')
@section('title')
    Addon Categories
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">Addon Categories</h4>
                </div>
                <div class="nk-block-head-content">
                    <a data-bs-toggle="modal" data-bs-target="#add_addon_category" class="btn btn-primary">
                        <em class="icon ni ni-plus"></em>
                        <span>Add Addon Category</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="table card-bordered" id="addon_categories" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="col">Id</th>
                                <th class="col">Name</th>
                                <th class="col">Restaurant</th>
                                <th class="col">Type</th>
                                <th class="col">Description</th>
                                <th class="col">Status</th>
                                <th class="col"></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @foreach ($addonCategories as $addonCategory)
        <div class="modal fade" id="deleteAddonCategory{{ $addonCategory->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Addon Category</h5>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </div>
                    </div>
                    <div class="modal-body">
                        <p>Do you really want to delete this Addon Category ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('admin.deleteAddonCategory', $addonCategory->id) }}" type="button"
                            class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="editAddonCategory{{ $addonCategory->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-top modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Addon Category</h5>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </div>
                    </div>
                    <form action="{{ route('admin.updateAddonCategory') }}" method="POST" enctype="multipart/form-data"
                        id="myForm">
                        @csrf
                        <div class="modal-body ">
                            <input type="hidden" name='id' value="{{ $addonCategory->id }}" required />
                            <div class='form-group row mb-4 align-middle'>
                                <div class="col-lg-6 mb-1">
                                    <label for='name' class="required form-label">Name</label>
                                    <input type="text" class="form-control" placeholder="Name" name='name'
                                        value="{{ $addonCategory->name }}" id='name' required />
                                </div>
                                <div class="col-lg-6 mb-1">
                                    <label for='description' class=" form-label">Description</label>
                                    <input type="text" class="form-control" placeholder="Description"
                                        value="{{ $addonCategory->description }}" name='description' id='description'
                                         />
                                </div>
                                <div class="col-lg-6 mb-1">
                                    <label for='type' class=" form-label">Type </label>
                                    <select class="form-select js-select2" data-control="select2"
                                        data-placeholder="Select an option" id="type" name='type'
                                        data-dropdown-parent="#editAddonCategory{{ $addonCategory->id }}"
                                        data-allow-clear="true" >
                                        <option value="" disabled selected>Select An Option</option>
                                        <option value="SINGLE" @if ($addonCategory->type == 'SINGLE') selected @endif>Single
                                        </option>
                                        <option value="MULTIPLE" @if ($addonCategory->type == 'MULTIPLE') selected @endif>Multiple
                                        </option>
                                    </select>
                                </div>
                                <div class="col-lg-6 mb-1">
                                    <label for='restaurant_id' class=" form-label">Restaurant </label>
                                    <select class="form-select js-select2" data-control="select2"
                                        data-placeholder="Select an option" id="restaurant_id" name='restaurant_id'
                                        data-dropdown-parent="#editAddonCategory{{ $addonCategory->id }}"
                                        data-allow-clear="true" >
                                        <option value="" disabled selected>Select An Option</option>
                                        @foreach ($restaurants as $restaurant)
                                            <option value="{{ $restaurant->id }}"
                                                @if ($addonCategory->restaurant_id == $restaurant->id) selected @endif>{{ $restaurant->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <div class="modal fade" id="add_addon_category" tabindex="-1">
        <div class="modal-dialog modal-dialog-top modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Addon Category</h5>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </div>
                </div>
                <form action="{{ route('admin.addAddonCategory') }}" method="POST" enctype="multipart/form-data"
                    id="myForm">
                    @csrf
                    <div class="modal-body ">
                        <div class='form-group row mb-4 align-middle'>
                            <div class="col-lg-6 mb-1">
                                <label for='name' class="required form-label">Name</label>
                                <input type="text" class="form-control" placeholder="Name" name='name'
                                    id='name' required />
                            </div>
                            <div class="col-lg-6 mb-1">
                                <label for='description' class=" form-label">Description</label>
                                <input type="text" class="form-control" placeholder="Description" name='description'
                                    id='description'  />
                            </div>
                            <div class="col-lg-6 mb-1">
                                <label for='type' class=" form-label">Type </label>
                                <select class="form-select js-select2" data-control="select2"
                                    data-placeholder="Select an option" id="type" name='type'
                                    data-dropdown-parent="#add_addon_category"
                                    data-allow-clear="true" >
                                    <option value="" disabled selected>Select An Option</option>
                                    <option value="SINGLE">Single</option>
                                    <option value="MULTIPLE">Multiple</option>
                                </select>
                            </div>
                            <div class="col-lg-6 mb-1">
                                <label for='restaurant_id' class=" form-label">Restaurant </label>
                                <select class="form-select js-select2" data-control="select2"
                                    data-placeholder="Select an option" id="restaurant_id" name='restaurant_id'
                                    data-dropdown-parent="#add_addon_category"
                                    data-allow-clear="true" >
                                    <option value="" disabled selected>Select An Option</option>
                                    @foreach ($restaurants as $restaurant)
                                        <option value="{{ $restaurant->id }}">{{ $restaurant->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var datatable = $('#addon_categories').DataTable({
                processing: true,
                serverSide: true,
                stateSave: true,
                responsive: true,
                // select: true,
                filter: true,
                dom: "<'row'" +
                    "<'col-6 d-flex align-items-center justify-content-start'f>" +
                    "<'col-6 d-flex align-items-center justify-content-end'l>" +
                    ">" +
                    "<'table-responsive my-2'tr>" +
                    "<'row'" +
                    "<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i>" +
                    "<'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
                    ">",
                order: [
                    [0, "desc"]
                ],
                ajax: '{{ route('admin.getAllAddonCategories') }}',
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name',
                        sortable: false
                    },
                    {
                        data: 'restaurant',
                        sortable: false
                    },
                    {
                        data: 'type',
                        sortable: false
                    },
                    {
                        data: 'description',
                        sortable: false
                    },
                    {
                        data: 'status',
                        sortable: false
                    },
                    {
                        data: 'action',
                        sortable: false
                    },
                ],

            });
            var search = document.querySelector('.search');
            html = search.innerHTML
            search.innerHTML = '<span class="fw-3 px-2">Show</span>' + html +
                '<span class="fw-3 px-2">Entries</span>'
        });
    </script>
@endsection
