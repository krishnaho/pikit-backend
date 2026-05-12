@extends('admin.layout.master')
@section('title')
    Banner
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">Banner</h4>
                </div>
                <div class="nk-block-head-content">
                    <a data-bs-toggle="modal" data-bs-target="#add_banner" class="btn btn-primary">
                        <em class="icon ni ni-plus"></em>
                        <span>Add Banner</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="table card-bordered" id="banners" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="col">ID</th>
                                <th class="col">Name</th>
                                <th class="col">Image</th>
                                <th class="col">Latitude</th>
                                <th class="col">Longitude</th>
                                <th class="col">Radius</th>
                                <th class="col">City</th>
                                <th class="col">Status</th>
                                <th class="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="add_banner" tabindex="-1">
        <div class="modal-dialog modal-dialog-top modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Banner</h5>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </div>
                </div>
                <form class="form" method="POST" action="{{ route('restaurantOwner.createBanner') }}"
                    enctype="multipart/form-data"> @csrf
                    <div class="modal-body py-10 px-lg-17">
                        <div class='form-group row mb-4 align-middle'>
                            <div class="col-lg-6 mb-1">
                                <label for='name' class="required form-label">Name</label>
                                <input type="text" class="form-control" placeholder="Name" name='name' id='name'
                                    required />
                            </div>

                            <div class="col-lg-6 mb-1">
                                <label for="" class="form-label required">Image:</label>
                                <div class="">
                                    <input name="image" class="form-control form-control-solid " type="file"
                                        accept="image/*" required>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-1">
                                <label for="" class="form-label required">
                                    Latitude:</label>
                                <div>
                                    <input type="text" name="latitude" required id=""
                                        placeholder="Enter Latitude" class="form-control form-control-solid " />
                                </div>
                            </div>
                            <div class="col-lg-6 mb-1">
                                <label for="" class="form-label required">
                                    Longitude:</label>
                                <div>
                                    <input type="text" name="longitude" required id=""
                                        placeholder="Enter Longitude" class="form-control form-control-solid " />
                                </div>
                            </div>
                            <div class="col-lg-6 mb-1">
                                <label for="" class="form-label  required">
                                    Radius:</label>
                                <div class="">
                                    <input type="number" name="radius" required id="" placeholder="Enter Radius"
                                        class="form-control form-control-solid " min="0">
                                </div>
                            </div>
                            <div class="col-lg-6 mb-1">
                                <label for="" class="form-label required">
                                    City:</label>
                                <div class="">
                                    <select name="city_id" required class="form-select js-select2" data-control="select2"
                                        data-dropdown-parent="#add_banner" data-placeholder="Select An Option"
                                        data-allow-clear="true">
                                        <option value="">Select an Option</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-1">
                                <label for="" class="form-label required">
                                    Restaurant Category:</label>
                                <div class="">
                                    <select class="form-select js-select2" data-control="select2"
                                        name="restaurant_category_id" required data-dropdown-parent="#add_banner"
                                        data-placeholder="Select An Option" data-allow-clear="true">
                                        <option value="">Select An Option</option>
                                        @foreach ($restaurantCategories as $restaurantCategory)
                                            <option value="{{ $restaurantCategory->id }}">
                                                {{ $restaurantCategory->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <input type="hidden" name='restaurant_id' value="{{ $restaurant->id }}" />
                            <input type="hidden" name='type' value="ITEM" />

                            <div class="col-lg-12 mb-1">
                                <label for="item_id" class=" form-label">Banner Applicable Items:</label>
                                <div>
                                    <select class="form-select js-select2" data-control="select2" id="item_id"
                                        name="item_id[]" data-dropdown-parent="#add_banner"  multiple data-placeholder="Select an option">
                                        @foreach ($items as $item)
                                            <option value="{{ $item->id }}" class="text-capitalize items_selectbox"
                                                {{ isset($banner) && in_array($item->id, $bannerItems) ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer flex-right">
                            <button type="reset" id="kt_modal_add_customer_cancel" class="btn btn-light me-3"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="kt_modal_add_customer_submit" class="btn btn-success">
                                Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($banners as $banner)
        <div class="modal fade" id="deleteBanner{{ $banner->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Banner</h5>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </div>
                    </div>
                    <div class="modal-body">
                        <p>Do you really want to delete this Banner ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('restaurantOwner.deleteBanner', $banner->id) }}" type="button"
                            class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        $(document).ready(function() {
            var datatable = $('#banners').DataTable({
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
                ajax: '{{ route('restaurantOwner.getAllBanners') }}',
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
                        data: 'latitude',
                        sortable: false
                    },
                    {
                        data: 'longitude',
                        sortable: false
                    },
                    {
                        data: 'radius',
                        sortable: false
                    },
                    {
                        data: 'city',
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
