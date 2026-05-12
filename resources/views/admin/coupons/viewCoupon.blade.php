@extends('admin.layout.master')
@section('title')
    Coupon
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">Coupon</h4>
                </div>
                <div class="nk-block-head-content">
                    <a data-bs-toggle="modal" data-bs-target="#add_Coupon" class="btn btn-primary">
                        <em class="icon ni ni-plus"></em>
                        <span>Add Coupon</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="table card-bordered" id="coupons" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="col">ID</th>
                                <th class="col">Name</th>
                                <th class="col">Image</th>
                                <th class="col">Type</th>
                                <th class="col">Coupon Code</th>
                                <th class="col">Discount Type</th>
                                <th class="col">Coupon Discount</th>
                                <th class="col">Expiry Date</th>
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
    <div class="modal fade" id="add_Coupon" tabindex="-1">
        <div class="modal-dialog modal-dialog-top modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Coupon</h5>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </div>
                </div>
                <form class="form" method="POST" action="{{ route('admin.createCoupon') }}"
                    enctype="multipart/form-data"> @csrf
                    <div class="modal-body py-10 px-lg-17">
                        <div class='form-group row mb-4 align-middle'>
                            <div class="col-lg-12 mb-2">
                                <label for='name' class="required form-label">Name</label>
                                <input type="text" class="form-control" placeholder="Name" name='name' id='name'
                                    required />
                            </div>

                            <div class="col-lg-6 mb-2">
                                <label for="" class="form-label required">Image:</label>
                                <div class="">
                                    <input name="image" class="form-control  " type="file" accept="image/*" required>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="description" class="form-label required">
                                    Description:</label>
                                <div>
                                    <input type="text" name="description" required id="description"
                                        placeholder="Description" class="form-control  " />
                                </div>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="coupon_code" class="form-label required">
                                    Coupon Code:</label>
                                <div>
                                    <input type="text" name="coupon_code" required id="coupon_code"
                                        placeholder="Coupon Code" class="form-control  " />
                                </div>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="discount_type" class="form-label required">
                                    Discount Type:</label>
                                <div class="">
                                    <select name="discount_type" id="discount_type" required class="form-select js-select2"
                                        data-control="select2" data-dropdown-parent="#add_Coupon"
                                        data-placeholder="Select An Option" data-allow-clear="true">
                                        <option value="" disabled>Select an Option</option>
                                        <option value="FIXED">Fixed</option>
                                        <option value="PERCENTAGE">Percentage</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="coupon_discount" class="form-label required">
                                    Coupon Discount:</label>
                                <div>
                                    <input type="number" name="coupon_discount" required id="coupon_discount"
                                        placeholder="Coupon Discount" class="form-control  " />
                                </div>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="max_discount" class="form-label required">
                                    Maximum Discount:</label>
                                <div>
                                    <input type="number" name="max_discount" required id="max_discount"
                                        placeholder="Maximum Discount" class="form-control  " />
                                </div>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="start_date" class="form-label required">
                                    Start Date:</label>
                                <div>
                                    <input type="date" name="start_date" required id="start_date"
                                        placeholder="Start Date" class="form-control  " />
                                </div>
                            </div>

                            <div class="col-lg-6 mb-2">
                                <label for="end_date" class="form-label required">
                                    End Date:</label>
                                <div>
                                    <input type="date" name="end_date" required id="end_date" placeholder="End Date"
                                        class="form-control  " />
                                </div>
                            </div>

                            <div class="col-lg-6 mb-2">
                                <label for="max_count" class="form-label required">
                                    Max No Of Use In Total:</label>
                                <div>
                                    <input type="text" name="max_count" required id="max_count"
                                        placeholder="Coupon Max Count" class="form-control  " />
                                </div>
                            </div>

                            <div class="col-lg-6 mb-2">
                                <label for="min_sub_total" class="form-label required">
                                    Min Sub Total:</label>
                                <div>
                                    <input type="text" name="min_sub_total" required id="min_sub_total"
                                        placeholder="Coupon Min Sub Total" class="form-control  " />
                                </div>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="sub_total_message" class="form-label required">
                                    Sub Total Not Reached Message:</label>
                                <div>
                                    <input type="text" name="sub_total_message" required id="sub_total_message"
                                        placeholder="End Date" class="form-control  " />
                                </div>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="" class="form-label required">
                                    User Type:</label>
                                <div class="user_type">
                                    <select name="city_id" id="user_type" required class="form-select js-select2"
                                        data-control="select2" data-dropdown-parent="#add_Coupon"
                                        data-placeholder="Select An Option" data-allow-clear="true">
                                        <option value="">Select an Option</option>
                                        <option value="ALL" class="text-capitalize">
                                            Unlimited Times For All Users
                                        </option>
                                        <option value="ONCENEW" class="text-capitalize">
                                            Once For New User For First Order
                                        </option>
                                        <option value="ONCE" class="text-capitalize">
                                            Once Per User
                                        </option>
                                        <option value="CUSTOM" class="text-capitalize">
                                            Define Custom Limit Per User
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="city_id" class="form-label required">
                                    City:</label>
                                <div class="">
                                    <select name="city_id" id="city_id" required class="form-select js-select2"
                                        data-control="select2" data-dropdown-parent="#add_Coupon"
                                        data-placeholder="Select An Option" data-allow-clear="true">
                                        <option value="">Select an Option</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label for="coupon_type" class="form-label required">
                                    Type :</label>
                                <div class="">
                                    <select class="form-select js-select2" data-control="select2"
                                        data-dropdown-parent="#add_Coupon" data-placeholder="Select an option"
                                        name="coupon_type" id="coupon_type" required onchange="changeView();">
                                        <option value="">Select An Option</option>
                                        <option value="RESTAURANT">Restaurant</option>
                                        <option value="ITEM">Item</option>
                                    </select>
                                </div>
                            </div>

                            <div id="restaurants" style="display: show" class="col-lg-12 mb-2">
                                <label for="restaurant_ids" class="form-label required">
                                    Coupon Applicable Restaurants </label>
                                <div class="">
                                    <select class="form-select js-select2" data-control="select2" id="restaurant"
                                        name="restaurant_ids[]" id="restaurant_ids" multiple
                                        data-dropdown-parent="#add_Coupon" data-placeholder="Select an option">
                                        <option value="" disabled>Select An Option</option>
                                        @foreach ($restaurants as $restaurant)
                                            <option value="{{ $restaurant->id }}" class="restaurant_selectbox">
                                                {{ $restaurant->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                            <div id="items" style="display: none" class="col-lg-12 mb-2">
                                <label for="item_id" class=" form-label">Coupon Applicable Items:</label>
                                <div>
                                    <select class="form-select js-select2" data-control="select2" id="item_id"
                                        name="item_id[]" multiple data-placeholder="Select an option">
                                        @foreach ($items as $item)
                                            <option value="{{ $item->id }}" class="text-capitalize items_selectbox">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <script>
                                function changeView() {
                                    var type = document.getElementById("coupon_type").value;
                                    if (type == "RESTAURANT") {
                                        $('#restaurants').show();
                                        $('#items').hide();
                                    } else if (type == "ITEM") {
                                        $('#restaurants').hide();
                                        $('#items').show();
                                    }
                                }
                            </script>
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

    @foreach ($coupons as $coupon)
        <div class="modal fade" id="deleteCoupon{{ $coupon->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Coupon</h5>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </div>
                    </div>
                    <div class="modal-body">
                        <p>Do you really want to delete this Coupon ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('admin.deleteCoupon', $coupon->id) }}" type="button"
                            class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        $(document).ready(function() {
            var datatable = $('#coupons').DataTable({
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
                ajax: '{{ route('admin.getAllCoupons') }}',
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
                        data: 'type',
                        sortable: false
                    },
                    {
                        data: 'coupon_code',
                        sortable: false
                    },
                    {
                        data: 'discount_type',
                        sortable: false
                    },
                    {
                        data: 'coupon_discount',
                        sortable: false
                    },
                    {
                        data: 'expiry_date',
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
    <script>
        $(document).ready(function() {
            $(document).on('change', '#city_id  , #type', function() {
                if ($('#type').val() == "ITEM") {
                    $.ajax({
                        type: "get",
                        url: '{{ url('/admin/get-city-items') }}',
                        data: {
                            city_id: $('#city_id').val(),
                        },
                        success: function(data) {
                            console.log(data)
                            $('.items_selectbox').remove();
                            $.each(data, function(key, item) {
                                document.getElementById('item_id').innerHTML +=
                                    '<option class="items_selectbox" value=' + item.id +
                                    '>' + item.name + '</option>'
                            });
                        }

                    })
                } else if ($('#type').val() == "RESTAURANT") {
                    $.ajax({
                        type: "get",
                        url: '{{ url('/admin/get-city-restaurants') }}',
                        data: {
                            city_id: $('#city_id').val(),
                        },
                        success: function(data) {
                            console.log(data)
                            $('.restaurant_selectbox').remove();
                            $.each(data, function(key, item) {
                                document.getElementById('restaurant_id').innerHTML +=
                                    '<option class="restaurant_selectbox" value=' + item
                                    .id +
                                    '>' + item.name + '</option>'
                            });
                        }

                    })
                }
            });
        });
    </script>
@endsection
