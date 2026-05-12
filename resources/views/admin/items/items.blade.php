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

                    <a data-bs-toggle="modal" data-bs-target="#upload_bulk" class="btn btn-primary">
                        <em class="icon ni ni-upload"></em>
                        <span>Upload Bulk</span>
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
                                <th class="col"><input type="checkbox" id="select-all"></th>
                                <!-- Checkbox for "Select All" -->
                                <th class="col">Id</th>
                                <th class="col">Name</th>
                                <th class="col">Image</th>
                                <th class="col">Price</th>
                                <th class="col">Item Category</th>
                                <th class="col">Restaurant</th>
                                <th class="col">Status</th>
                                <th class="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <button id="delete-selected" class="btn btn-danger">Delete Selected</button>
                    <!-- Button to delete selected items -->
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
                        <a href="{{ route('admin.deleteItem', $item->id) }}" type="button"
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
                                <input type="file" class="form-control" placeholder="Image" name='image'
                                    id='image' />
                            </div>
                            <div class="col-lg-12 mb-1">
                                <label for='description' class="required form-label">Description</label>
                                <input type="text" class="form-control" placeholder="Description" name='description'
                                    id='description' />
                            </div>
                            <div class="col-lg-6 mb-1">
                                <label for='restaurant_id' class=" form-label">Restaurant </label>
                                <select class="form-select js-select2" data-control="select2"
                                    data-placeholder="Select an option" id="restaurant_id" name='restaurant_id'
                                    data-dropdown-parent="#add_item" data-allow-clear="true">
                                    <option value="" disabled selected>Select An Option</option>
                                    @foreach ($restaurants as $restaurant)
                                        <option value="{{ $restaurant->id }}">{{ $restaurant->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6  mb-1">
                                <label for='item_category_id' class=" form-label">Item Category</label>
                                <select class="form-select js-select2" data-control="select2"
                                    data-placeholder="Select an option" id="item_category_id" name='item_category_id'
                                    data-dropdown-parent="#add_item" data-allow-clear="true">
                                    <option value="" disabled selected>Select An Option</option>
                                </select>
                            </div>
                        </div>
                        <div class='form-group row' id="withOutDiscount">
                            <div class="col-lg-6 mb-1">
                                <label for='price' class=" form-label">Price</label>
                                <input type="number" step="any" class="form-control" placeholder="Price"
                                    name='price' id='price' />
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
                                <label for='selling_price' class=" form-label">Selling Price</label>
                                <input type="number" step="any" class="form-control" placeholder="Selling Price"
                                    name='selling_price' id='selling_price' />
                            </div>
                            <div class="col-lg-5 mb-1">
                                <label for='market_price' class=" form-label">Market Price</label>
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
                                <label for='min_quantity' class=" form-label">Min Quantity</label>
                                <input type="number" class="form-control" placeholder="Min Quantity"
                                    name='min_quantity' id='min_quantity' />
                            </div>
                            <div class="col-lg-6 mb-1">
                                <label for='max_quantity' class=" form-label">Max Quantity</label>
                                <input type="number" class="form-control" placeholder="Max Quantity"
                                    name='max_quantity' id='max_quantity' />
                            </div>
                            <div class="col-lg-6 mb-1">
                                <label for='commision_rate' class=" form-label">Commision</label>
                                <input type="number" step="any" class="form-control" placeholder="Commision"
                                    name='commision_rate' id='commision_rate' />
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="item_id" class=" form-label">Choose Applicable Item Groups:</label>
                                <div>
                                    <select class="form-select js-select2" data-control="select2" id="item_id"
                                        name="item_groups_id[]" multiple data-placeholder="Select an option">
                                        @foreach ($itemGroups as $item)
                                            <option value="{{ $item->id }}" class="text-capitalize items_selectbox">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
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

    <div class="modal fade" id="upload_bulk" tabindex="-1">
        <div class="modal-dialog modal-dialog-top modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Item</h5>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </div>
                </div>
                <form action="{{ route('admin.viewItemBulkUpload') }}" method="POST" enctype="multipart/form-data"
                    id="myForm">
                    @csrf
                    <div class="modal-body ">
                        <div class='form-group row mb-1 align-middle'>

                            <div class="col-lg-6 mb-1">
                                <label name='csv' for='csv' class="required form-label">CSV File</label>
                                <input type="file" class="form-control" placeholder="CSV File" name='csv'
                                    id='csv' accept=".csv" required />
                            </div>

                            <div class="col-lg-6 mb-1">
                                <label name='image' for='image' class="form-label">Download</label>
                                <a href="{{ asset('csv/SampleCSVFile.csv') }}"
                                    class="btn btn-outline-secondary form-control" style="border: 1px dashed">Download
                                    Sample CSV</a>
                            </div>

                        </div>
                    </div>
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
                    [1, "desc"]
                ],
                ajax: '{{ route('admin.getAllItems') }}',
                columns: [{
                        data: 'id',
                        render: function(data) {
                            return '<input type="checkbox" class="item-checkbox" value="' + data +
                                '">';
                        },
                        orderable: false
                    },
                    {
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
                        data: 'restaurant',
                        sortable: false
                    },
                    {
                        data: 'status',
                        sortable: false
                    },
                    {
                        data: 'action',
                        sortable: false
                    }
                ]
            });


            $('#restaurant_id').on('change', function() {
                console.log($(this).val())
                $.ajax({
                    url: '{{ route('admin.getRestaurantItemCategories') }}',
                    data: {
                        'restaurant_id': $(this).val()
                    },
                    success: function(data) {
                        console.log(data)
                        document.getElementById('item_category_id').innerHTML = '';
                        $.each(data, function(index, value) {
                            document.getElementById('item_category_id').innerHTML +=
                                '<option class="item-remove" value="' + value.id +
                                '">' + value
                                .name + '</option>'
                        });
                    }
                })
            });

            // Select/Deselect all checkboxes
            $('#select-all').on('click', function() {
                var rows = datatable.rows({
                    'search': 'applied'
                }).nodes();
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });

            // If one checkbox is unchecked, uncheck the "Select All" checkbox
            $('#items tbody').on('change', '.item-checkbox', function() {
                if (!this.checked) {
                    $('#select-all').prop('checked', false);
                }
            });

            // Delete selected items
            $('#delete-selected').on('click', function() {
                var selectedItems = [];
                $('.item-checkbox:checked').each(function() {
                    selectedItems.push($(this).val());
                });

                if (selectedItems.length > 0) {
                    console.log(selectedItems);

                    // Send AJAX request to delete the selected items
                    $.ajax({
                        url: '{{ route('admin.selectDelete') }}',
                        method: 'POST',
                        data: {
                            ids: selectedItems,
                            _token: '{{ csrf_token() }}' // Include CSRF token
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('Selected items deleted successfully.');
                                datatable.ajax.reload();
                            }
                        },
                        error: function(xhr) {
                            alert('Error deleting items.');
                        }
                    });
                } else {
                    alert('Please select at least one item to delete.');
                }
            });
        });
    </script>
@endsection
