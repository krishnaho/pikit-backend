@extends('admin.layout.master')
@section('title')
    Items
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">Items</h4>
                </div>
                <div class="nk-block-head-content">
                    <a data-bs-toggle="modal" data-bs-target="#bulk_upload" class="btn btn-secondary">
                        <em class="icon ni ni-upload"></em>
                        <span>Bulk Upload</span>
                    </a>
                    <a data-bs-toggle="modal" data-bs-target="#add_item" class="btn btn-primary">
                        <em class="icon ni ni-plus"></em>
                        <span>Add Item</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="table card-bordered" id="items" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="col">Id</th>
                                <th class="col">Name</th>
                                <th class="col">Image</th>
                                <th class="col">Price</th>
                                <th class="col">Item Category</th>
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

    @foreach ($items as $item)
        <div class="modal fade" id="deleteItem{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Item</h5>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </div>
                    </div>
                    <div class="modal-body">
                        <p>Do you really want to delete this Item ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('restaurantOwner.deleteItem', $item->id) }}" type="button"
                            class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="modal fade" id="add_item" tabindex="-1">
        <div class="modal-dialog modal-dialog-top modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Item</h5>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </div>
                </div>
                <form action="{{ route('admin.addItem') }}" method="POST" enctype="multipart/form-data" id="myForm">
                    @csrf
                    <div class="modal-body ">
                        <div class='form-group row mb-1 align-middle'>
                            <div class="col-lg-6 mb-1">
                                <label for='name' class="required form-label">Name</label>
                                <input type="text" class="form-control" placeholder="Name" name='name' id='name'
                                    required />
                            </div>
                            <div class="col-lg-6 mb-1">
                                <label name='image' for='image' class="required form-label">Image</label>
                                <input type="file" class="form-control" placeholder="Image" name='image' id='image'
                                    required />
                            </div>
                            <div class="col-lg-6 mb-1">
                                <label for='description' class="required form-label">Description</label>
                                <input type="text" class="form-control" placeholder="Description" name='description'
                                    id='description' required />
                            </div>
                            <input type="hidden" name='restaurant_id' value="{{ $restaurant->id }}" />

                            <div class="col-lg-6  mb-1">
                                <label for='item_category_id' class="required form-label">Item Category</label>
                                <select class="form-select js-select2" data-control="select2"
                                    data-placeholder="Select an option" id="item_category_id" name='item_category_id'
                                    data-dropdown-parent="#add_item" data-allow-clear="true">
                                    <option value="" disabled selected>Select An Option</option>
                                    @foreach ($itemCategories as $itemCategory)
                                        <option value="{{ $itemCategory->id }}">{{ $itemCategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class='form-group row' id="withOutDiscount">
                            <div class="col-lg-6 mb-1">
                                <label for='price' class="required form-label">Price</label>
                                <input type="number" step="any" class="form-control" placeholder="Price"
                                    name='price' id='price' required />
                            </div>
                            <div class="col-lg-6 align-end  mb-1">
                                <button type="button" class="btn btn-secondary" value="ADD"
                                    onclick="changeType(this)">
                                    Add Discount
                                </button>
                            </div>
                            <input type="hidden" id="check" name="check">
                        </div>

                        <div class='form-group row' id="withDiscount" style="display:none">
                            <div class="col-lg-6 mb-1">
                                <label for='selling_price' class="required form-label">Selling Price</label>
                                <input type="number" step="any" class="form-control" placeholder="Selling Price"
                                    name='selling_price' id='selling_price' />
                            </div>
                            <div class="col-lg-5 mb-1">
                                <label for='market_price' class="required form-label">Market Price</label>
                                <input type="number" step="any" class="form-control" placeholder="Market Price"
                                    name='market_price' id='market_price' />
                            </div>
                            <div class="col-lg-1 align-end  mb-1">
                                <button type="button" class="btn btn-secondary" value="REMOVE"
                                    onclick="changeType(this)">
                                    -
                                </button>
                            </div>
                        </div>
                        <div class='form-group row  align-middle'>
                            <div class="col-lg-6 mb-1">
                                <label for='min_quantity' class="required form-label">Min Quantity</label>
                                <input type="number" class="form-control" placeholder="Min Quantity"
                                    name='min_quantity' id='min_quantity' />
                            </div>
                            <div class="col-lg-6 mb-1">
                                <label for='max_quantity' class="required form-label">Max Quantity</label>
                                <input type="number" class="form-control" placeholder="Max Quantity"
                                    name='max_quantity' id='max_quantity' />
                            </div>
                            <div class="col-lg-6 mb-1">
                                <label for='commision_rate' class="required form-label">Commision</label>
                                <input type="number" step="any" class="form-control" placeholder="Commision"
                                    name='commision_rate' id='commision_rate' />
                            </div>
                            <div class="col-lg-12  mt-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="is_popular"
                                        id="is_popular_add">
                                    <label class="custom-control-label" for="is_popular_add">Is Popular</label>
                                </div>
                            </div>
                            <div class="col-lg-12  mt-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="is_recommended"
                                        id="is_recommended_add">
                                    <label class="custom-control-label" for="is_recommended_add">Is Recommended</label>
                                </div>
                            </div>
                            <div class="col-lg-12  mt-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="is_veg" id="is_veg_add">
                                    <label class="custom-control-label" for="is_veg_add">Is Veg</label>
                                </div>
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

    <div class="modal fade" id="bulk_upload" tabindex="-1">
        <div class="modal-dialog modal-dialog-top modal-xl">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Upload Items</h5>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </div>
                </div>

                <!-- Modal Form -->
                <form action="{{ route('admin.viewItemBulkUploadStore') }}" method="POST" enctype="multipart/form-data" id="bulkUploadForm">
                    @csrf
                    <div class="modal-body">
                        <div class='form-group row mb-1 align-middle'>

                            <!-- CSV Upload Field -->
                            <div class="col-lg-6 mb-1">
                                <label name='csv' for='csv' class="required form-label">CSV File</label>
                                <input type="file" class="form-control" placeholder="CSV File" name='csv'
                                    id='csv' accept=".csv" required />
                            </div>

                            <!-- Sample CSV Download -->
                            <div class="col-lg-6 mb-1">
                                <label name='image' for='image' class="form-label">Download</label>
                                <a href="{{ asset('csv/zeato.csv') }}" class="btn btn-outline-secondary form-control"
                                    style="border: 1px dashed">Download Sample CSV</a>
                            </div>

                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        var price = document.getElementById("price");
        var selling_price = document.getElementById("selling_price");
        var market_price = document.getElementById("market_price");

        function changeType(element) {
            var value = $(element).val()
            if (value == "ADD") {
                $('#withOutDiscount').hide();
                $('#withDiscount').show();
                price.removeAttribute("required");
                selling_price.setAttribute("required", "required");
                market_price.setAttribute("required", "required");
                $('#check').val(1);
            } else if (value == "REMOVE") {
                $('#withOutDiscount').show();
                $('#withDiscount').hide();
                price.setAttribute("required", "required");
                selling_price.removeAttribute("required");
                market_price.removeAttribute("required");
                $('#market_price').val(0);
                $('#check').val(0);
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            var datatable = $('#items').DataTable({
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
                ajax: '{{ route('restaurantOwner.getAllItems') }}',
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
                        data: 'price',
                        sortable: false
                    },
                    {
                        data: 'itemCategory',
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
        });
    </script>
@endsection
