@extends('admin.layout.master')
@section('title')
    Item Group
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">Item Group</h4>
                </div>
                <div class="nk-block-head-content">
                    <a data-bs-toggle="modal" data-bs-target="#add_item_group" class="btn btn-primary">
                        <em class="icon ni ni-plus"></em>
                        <span>Add Item Group</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="table card-bordered" id="item_group" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="col">Id</th>
                                <th class="col">Name</th>
                                <th class="col">Restaurant Category</th>
                                <th class="col">Image</th>
                                <th class="col">Background Image</th>
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

    @foreach ($itemGroups as $itemGroup)
        <div class="modal fade" id="deleteItemGroup{{ $itemGroup->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Item Group</h5>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </div>
                    </div>
                    <div class="modal-body">
                        <p>Do you really want to delete this Item Group ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('admin.deleteItemGroup', $itemGroup->id) }}" type="button"
                            class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="editItemGroup{{ $itemGroup->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-top modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Item Group</h5>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </div>
                    </div>
                    <form action="{{ route('admin.updateItemGroup') }}" method="POST" enctype="multipart/form-data"
                        id="myForm">
                        @csrf
                        <div class="modal-body ">
                            <input type="hidden" name='id' value="{{ $itemGroup->id }}" required />

                            <div class='form-group row mb-4 align-middle'>
                                <div class="col-lg-6 mb-1">
                                    <label for='name' class="required form-label">Name</label>
                                    <input type="text" class="form-control" placeholder="Name" name='name'
                                        value="{{ $itemGroup->name }}" id='name' required />
                                </div>

                                <div class="col-lg-6 mb-1">
                                    <label for='restaurant_category_id' class=" form-label">Restaurant
                                        Category</label>
                                    <select class="form-select js-select2" data-control="select2"
                                        data-placeholder="Select an option" id="restaurant_category_id"
                                        name='restaurant_category_id'
                                        data-dropdown-parent="#editItemGroup{{ $itemGroup->id }}" data-allow-clear="true">
                                        <option value="" disabled selected>Select An Option</option>
                                        @foreach ($restaurantCategories as $restaurantCategory)
                                            <option value="{{ $restaurantCategory->id }}"
                                                @if ($itemGroup->restaurant_category_id == $restaurantCategory->id) selected @endif>{{ $restaurantCategory->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 mb-1">
                                    <label for='image' class=" form-label">Image</label>
                                    <input type="file" class="form-control" placeholder="Image" name='image'
                                        id='image' />
                                </div>
                                <div class="col-lg-6 mb-1">
                                    <label for='background_image' class=" form-label">Background Image</label>
                                    <input type="file" class="form-control" placeholder="Background Image" name='background_image'
                                        id='background_image' />
                                </div>
                                <div class="col-lg-6 mb-1">
                                    <label class=" form-label">Current Image</label>
                                    <div >
                                        @if ($itemGroup->image)
                                            <img src="{{ asset($itemGroup->image) }}"
                                                style=" height:6rem;object-fit:cover;border-radius:5px; " />
                                        @else
                                            <span class="badge badge-dot bg-info">No Image Found</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-1">
                                    <label class=" form-label">Current Background Image</label>
                                    <div >
                                        @if ($itemGroup->background_image)
                                            <img src="{{ asset($itemGroup->background_image) }}"
                                                style=" height:6rem;object-fit:cover;border-radius:5px; " />
                                        @else
                                            <span class="badge badge-dot bg-info">No Image Found</span>
                                        @endif
                                    </div>
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

    <div class="modal fade" id="add_item_group" tabindex="-1">
        <div class="modal-dialog modal-dialog-top modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Item Group</h5>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </div>
                </div>
                <form action="{{ route('admin.addItemGroup') }}" method="POST" enctype="multipart/form-data"
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
                                <label for='restaurant_category_id' class=" form-label">Restaurant
                                    Category</label>
                                <select class="form-select js-select2" data-control="select2"
                                    data-placeholder="Select an option" id="restaurant_category_id"
                                    name='restaurant_category_id' data-dropdown-parent="#add_item_group"
                                    data-allow-clear="true">
                                    <option value="" disabled selected>Select An Option</option>
                                    @foreach ($restaurantCategories as $restaurantCategory)
                                        <option value="{{ $restaurantCategory->id }}">{{ $restaurantCategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-1">
                                <label name='image' for='image' class=" form-label">Image</label>
                                <input type="file" class="form-control" placeholder="Image" name='image'
                                    id='image'  />
                            </div>
                            <div class="col-lg-6 mb-1">
                                <label for='background_image' class=" form-label">Background Image</label>
                                <input type="file" class="form-control" placeholder="Background Image" name='background_image'
                                    id='background_image'  />
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
            var datatable = $('#item_group').DataTable({
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
                ajax: '{{ route('admin.getAllItemGroups') }}',
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name',
                        sortable: false
                    },
                    {
                        data: 'restaurant_category',
                        sortable: false
                    },
                    {
                        data: 'image',
                        sortable: false
                    },
                    {
                        data: 'background_image',
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
