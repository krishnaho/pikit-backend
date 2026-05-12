@extends('admin.layout.master')
@section('title')
Addon
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">Addon</h4>
                </div>
                <div class="nk-block-head-content">
                    <a data-bs-toggle="modal" data-bs-target="#add_addon" class="btn btn-primary">
                        <em class="icon ni ni-plus"></em>
                        <span>Add Addon</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="table card-bordered" id="addon" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="col">Id</th>
                                <th class="col">Name</th>
                                <th class="col">Price</th>
                                <th class="col">Addon Category</th>
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

    @foreach ($addons as $addon)
        <div class="modal fade" id="deleteAddon{{ $addon->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Addon</h5>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </div>
                    </div>
                    <div class="modal-body">
                        <p>Do you really want to delete this Addon ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('restaurantOwner.deleteAddon', $addon->id) }}" type="button"
                            class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="editAddon{{ $addon->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-top modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Addon</h5>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </div>
                    </div>
                    <form action="{{ route('restaurantOwner.updateAddon') }}" method="POST" enctype="multipart/form-data"
                        id="myForm">
                        @csrf
                        <div class="modal-body ">
                            <input type="hidden" name='id' value="{{ $addon->id }}" required />
                            <div class='form-group row mb-4 align-middle'>
                                <div class="col-lg-6 mb-1">
                                    <label for='name' class="required form-label">Name</label>
                                    <input type="text" class="form-control" placeholder="Name" name='name'
                                        value="{{ $addon->name }}" id='name' required />
                                </div>
                                <div class="col-lg-6 mb-1">
                                    <label for='addon_category_id' class="required form-label">Addon Category </label>
                                    <select class="form-select js-select2" data-control="select2"
                                        data-placeholder="Select an option" id="addon_category_id" name='addon_category_id'
                                        data-dropdown-parent="#editAddon{{ $addon->id }}"  >
                                        <option value="" disabled>Select An Option</option>
                                        @foreach ($addonCategories as $addonCategory)
                                            <option value="{{ $addonCategory->id }}"
                                                @if ($addon->addon_category_id == $addonCategory->id) selected @endif>{{ $addonCategory->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 mb-1">
                                    <label for='price' class="required form-label">Price</label>
                                    <input type="number" step="any" class="form-control" placeholder="Price" name='price'
                                    value="{{ $addon->price }}"  id='price' required />
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

    <div class="modal fade" id="add_addon" tabindex="-1">
        <div class="modal-dialog modal-dialog-top modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Addon</h5>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </div>
                </div>
                <form action="{{ route('restaurantOwner.addAddon') }}" method="POST" enctype="multipart/form-data"
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
                                <label for='addon_category_id' class="required form-label">Addon Category </label>
                                <select class="form-select js-select2" data-control="select2"
                                    data-placeholder="Select an option" id="addon_category_id" name='addon_category_id'
                                    data-dropdown-parent="#add_addon" data-allow-clear="true">
                                    <option value="" disabled selected>Select An Option</option>
                                    @foreach ($addonCategories as $addonCategory)
                                        <option value="{{ $addonCategory->id }}">{{ $addonCategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-1">
                                <label for='price' class="required form-label">Price</label>
                                <input type="text" class="form-control" placeholder="Price" name='price'
                                    id='price' required />
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
            var datatable = $('#addon').DataTable({
                processing: true,
                serverSide: true,
                stateSave: true,
                responsive: true,
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
                ajax: '{{ route('restaurantOwner.getAllAddons') }}',
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name',
                        sortable: false
                    },
                    {
                        data: 'price',
                        sortable: false
                    },
                    {
                        data: 'addon_category',
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
