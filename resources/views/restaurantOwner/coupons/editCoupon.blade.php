@extends('admin.layout.master')
@section('title')
    Edit Coupon
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">Edit Coupon <span class="badge bg-purple">
                            {{ $coupon->name }}</span></h4>
                </div>
            </div>
        </div>
        <div class="card">
            <form class="form" method="POST" action="{{ route('restaurantOwner.updateCoupon') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $coupon->id }}">
                <div class="card-body py-10 px-lg-17">
                    <div class='form-group row mb-4 align-middle'>
                        <div class="col-lg-6 mb-2">
                            <label for='name' class="required form-label">Name</label>
                            <input type="text" class="form-control" placeholder="Name" name='name' id='name'
                                value="{{ $coupon->name }}" required />
                        </div>

                        <div class="col-lg-6 mb-2">
                            <label for="" class="form-label required">Image:</label>
                            <div class="">
                                <input name="image" class="form-control  " type="file" accept="image/*">
                            </div>
                        </div>
                        <div class="col-lg-12 mb-1">
                            <label for="currt_image" class="form-label col-lg-3">Current Image:</label>
                            <div>
                                @if ($coupon->image)
                                    <img src="{{ asset($coupon->image) }}" alt="" id="currt_image"
                                        style="height: 8rem; object-fit:contain">
                                @else
                                    <span class="badge badge-pill badge-info" id="currt_image">NO IMAGE AVAILAVLE!!</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label for="description" class="form-label required">
                                Description:</label>
                            <div>
                                <input type="text" name="description" required id="description" placeholder="Description"
                                    value="{{ $coupon->description }}" class="form-control  " />
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label for="coupon_code" class="form-label required">
                                Coupon Code:</label>
                            <div>
                                <input type="text" name="coupon_code" required id="coupon_code" placeholder="Coupon Code"
                                    value="{{ $coupon->coupon_code }}" class="form-control  " />
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label for="discount_type" class="form-label required">
                                Discount Type:</label>
                            <div class="">
                                <select name="discount_type" id="discount_type" required class="form-select js-select2"
                                    data-control="select2" data-placeholder="Select An Option" data-allow-clear="true">
                                    <option value="" disabled>Select an Option</option>
                                    <option value="FIXED" @if ($coupon->discount_type == 'FIXED') selected @endif>Fixed</option>
                                    <option value="PERCENTAGE" @if ($coupon->discount_type == 'PERCENTAGE') selected @endif>Percentage
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label for="coupon_discount" class="form-label required">
                                Coupon Discount:</label>
                            <div>
                                <input type="number" name="coupon_discount" required id="coupon_discount"
                                    value="{{ $coupon->coupon_discount }}" placeholder="Coupon Discount"
                                    class="form-control  " />
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label for="max_discount" class="form-label required">
                                Maximum Discount:</label>
                            <div>
                                <input type="number" name="max_discount" required id="max_discount"
                                    value="{{ $coupon->max_discount }}" placeholder="Maximum Discount"
                                    class="form-control  " />
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label for="start_date" class="form-label required">
                                Start Date:</label>
                            <div>
                                <input type="date" name="start_date" required id="start_date" placeholder="Start Date"
                                    value="{{ $coupon->start_date }}" class="form-control  " />
                            </div>
                        </div>

                        <div class="col-lg-6 mb-2">
                            <label for="end_date" class="form-label required">
                                End Date:</label>
                            <div>
                                <input type="date" name="end_date" required id="end_date" placeholder="End Date"
                                    value="{{ $coupon->end_date }}" class="form-control  " />
                            </div>
                        </div>

                        <div class="col-lg-6 mb-2">
                            <label for="max_count" class="form-label required">
                                Max No Of Use In Total:</label>
                            <div>
                                <input type="text" name="max_count" required id="max_count"
                                    value="{{ $coupon->max_count }}" placeholder="Coupon Max Count"
                                    class="form-control  " />
                            </div>
                        </div>

                        <div class="col-lg-6 mb-2">
                            <label for="min_sub_total" class="form-label required">
                                Min Sub Total:</label>
                            <div>
                                <input type="text" name="min_sub_total" required id="min_sub_total"
                                    value="{{ $coupon->min_sub_total }}" placeholder="Coupon Min Sub Total"
                                    class="form-control  " />
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label for="sub_total_message" class="form-label required">
                                Sub Total Not Reached Message:</label>
                            <div>
                                <input type="text" name="sub_total_message" required id="sub_total_message"
                                    value="{{ $coupon->sub_total_message }}" placeholder="End Date"
                                    class="form-control  " />
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <label for="" class="form-label required">
                                User Type:</label>
                            <div class="user_type">
                                <select name="user_type" id="user_type" required class="form-select js-select2"
                                    data-control="select2" data-placeholder="Select An Option" data-allow-clear="true">
                                    <option value="">Select an Option</option>
                                    <option value="ALL" @if ($coupon->user_type == 'ALL') selected @endif
                                        class="text-capitalize">
                                        Unlimited Times For All Users
                                    </option>
                                    <option value="ONCENEW" @if ($coupon->user_type == 'ONCENEW') selected @endif
                                        class="text-capitalize">
                                        Once For New User For First Order
                                    </option>
                                    <option value="ONCE" @if ($coupon->user_type == 'ONCE') selected @endif
                                        class="text-capitalize">
                                        Once Per User
                                    </option>
                                    <option value="CUSTOM" @if ($coupon->user_type == 'CUSTOM') selected @endif
                                        class="text-capitalize">
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
                                    data-control="select2" data-placeholder="Select An Option" data-allow-clear="true">
                                    <option value="">Select an Option</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}"
                                            @if ($coupon->city_id == $city->id) selected @endif>{{ $city->name }}
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
                                    data-placeholder="Select an option" name="coupon_type" id="coupon_type" required
                                    onchange="changeView();">
                                    <option value="">Select An Option</option>
                                    <option value="RESTAURANT" @if ($coupon->coupon_type == 'RESTAURANT') selected @endif>Restaurant</option>
                                    <option value="ITEM" @if ($coupon->coupon_type == 'ITEM') selected @endif>Item</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name='restaurant_id' value="{{ $restaurant->id }}" />
                        <div id="items" @if ($coupon->coupon_type == 'ITEM') style="display: show" @else style="display: none" @endif  class="col-lg-12 mb-2">
                            <label for="item_id" class=" form-label">Coupon Applicable Items:</label>
                            <div>
                                <select class="form-select js-select2" data-control="select2" id="item_id"
                                    name="item_id[]" multiple data-placeholder="Select an option">
                                    @foreach ($items as $item)
                                        <option value="{{ $item->id }}"
                                            @if (isset($coupon) && in_array($item->id, $couponItems)) selected @endif
                                            class="text-capitalize items_selectbox">
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
                                    $('#items').hide();
                                } else if (type == "ITEM") {
                                    $('#items').show();
                                }
                            }
                        </script>
                        <div class="text-right mt-4">
                            <button type="reset" id="kt_modal_add_customer_cancel" class="btn btn-light me-3"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="kt_modal_add_customer_submit" class="btn btn-success">
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
