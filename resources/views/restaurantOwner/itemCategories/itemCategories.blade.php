@extends('admin.layout.master')
@section('title')
    Item Categories
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">Item Categories</h4>
                </div>
                <div class="nk-block-head-content">
                    <a data-bs-toggle="modal" data-bs-target="#add_item_category" class="btn btn-primary">
                        <em class="icon ni ni-plus"></em>
                        <span>Add Item Category</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="table card-bordered" id="restaurant_categories" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="col">Id</th>
                                <th class="col">Name</th>
                                <th class="col">Image</th>
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

    @foreach ($itemCategories as $itemCategory)
        <div class="modal fade" id="deleteItemCategory{{ $itemCategory->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Item Category</h5>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </div>
                    </div>
                    <div class="modal-body">
                        <p>Do you really want to delete this Item Category ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('restaurantOwner.deleteItemCategory', $itemCategory->id) }}" type="button"
                            class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="editItemCategory{{ $itemCategory->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-top modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Item Category</h5>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </div>
                    </div>
                    <form action="{{ route('restaurantOwner.updateItemCategory') }}" method="POST" enctype="multipart/form-data"
                        id="myForm">
                        @csrf
                        <div class="modal-body ">
                            <input type="hidden" name='id' value="{{ $itemCategory->id }}" required />

                            <div class='form-group row mb-4 align-middle'>
                                <div class="col-lg-6 mb-1">
                                    <label for='name' class="required form-label">Name</label>
                                    <input type="text" class="form-control" placeholder="Name" name='name'
                                        value="{{ $itemCategory->name }}" id='name' required />
                                </div>
                                <div class="col-lg-6 mb-1">
                                    <label name='image' for='image' class="required form-label">Image</label>
                                    <input type="file" class="form-control" placeholder="Image" name='image'
                                        id='image' />
                                </div>
                                <div class="col-lg-12 mb-1">
                                    <label for='description' class="required form-label">Description</label>
                                    <input type="text" class="form-control" placeholder="Description"
                                    value="{{ $itemCategory->description }}"   name='description' id='description' required />
                                </div>
                                <div class='form-group row mb-1 align-middle'>
                                    <label class=" col-lg-3 required form-label">Current Image</label>
                                    <div class="col-lg-9">
                                        @if ($itemCategory->image)
                                            <img src="{{ asset($itemCategory->image) }}"
                                                style=" height:6rem;object-fit:cover;border-radius:5px; " />
                                        @else
                                            <span class="badge badge-dot bg-info">No Image Found</span>
                                        @endif
                                    </div>
                                </div>
                                <input type="hidden" name='restaurant_id' value="{{ $restaurant->id }}" required />
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

    <div class="modal fade" id="add_item_category" tabindex="-1">
        <div class="modal-dialog modal-dialog-top modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Item Category</h5>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </div>
                </div>
                <form action="{{ route('restaurantOwner.addItemCategory') }}" method="POST" enctype="multipart/form-data"
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
                                <label name='image' for='image' class="required form-label">Image</label>
                                <input type="file" class="form-control" placeholder="Image" name='image'
                                    id='image' required />
                            </div>
                            <input type="hidden" name='restaurant_id' value="{{ $restaurant->id }}" />
                            <div class="col-lg-6 mb-1">
                                <label for='description' class="required form-label">Description</label>
                                <input type="text" class="form-control" placeholder="Description" name='description'
                                    id='description' required />
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
            var datatable = $('#restaurant_categories').DataTable({
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
                ajax: '{{ route('restaurantOwner.getAllItemCategories') }}',
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name',
                        sortable: false
                    },
                    {
                        data: 'image',
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
