@extends('admin.layout.master')
@section('title')
    Edit Banner
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">Edit Banner <span class="badge bg-purple">
                            {{ $banner->name }}</span></h4>
                </div>
            </div>
        </div>
        <div class="card">
            <form class="form" method="POST" action="{{ route('admin.updateBanner') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $banner->id }}">
                <div class="card-body py-10 px-lg-17">
                    <div class="form-group row">
                        <div class="col-lg-6 mb-1">
                            <label for="name" class="form-label required"> Name:</label>
                            <div>
                                <input type="text" name="name" required id="name" value="{{ $banner->name }}"
                                    placeholder="Enter Banner Name" class="form-control ">
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <label for="image" class="form-label col-lg-3">Image:</label>
                            <div>
                                <input name="image" class="form-control" type="file" id="image"
                                    @if ($banner->image) @else required @endif />
                            </div>
                        </div>
                        <div class="col-lg-12 mb-1">
                            <label for="currt_image" class="form-label col-lg-3">Current Image:</label>
                            <div>
                                @if ($banner->image)
                                    <img src="{{ asset($banner->image) }}" alt="" id="currt_image"
                                        style="height: 8rem; object-fit:contain">
                                @else
                                    <span class="badge badge-pill badge-info" id="currt_image">NO IMAGE AVAILAVLE!!</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <label for="latitude" class="form-label required">
                                Latitude:</label>
                            <div>
                                <input type="text" name="latitude" required id="latitude" placeholder="Enter Latitude"
                                    value="{{ $banner->latitude }}" class="form-control " />
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <label for="longitude" class="form-label required">
                                Longitude:</label>
                            <div>
                                <input type="text" name="longitude" required id="longitude" placeholder="Enter Longitude"
                                    value="{{ $banner->longitude }}" class="form-control " />
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <label for="" class="form-label   required"> Radius:</label>
                            <div>
                                <input type="number" name="radius" required id="" min="0"
                                    placeholder="Enter Radius" class="form-control " value="{{ $banner->radius }}">
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <label for="" class="form-label  required"> City:</label>
                            <div>
                                <select name="city_id" id="city_id" required class="form-select js-select2"
                                    data-control="select2" data-placeholder="Select An Option">
                                    <option value="" disabled>Select a Option</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}"
                                            @if ($city->id == $banner->city_id) selected @endif>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-1">
                            <label for="" class="form-label required"> Restaurant Category:</label>
                            <div>
                                <select name="restaurant_category_id" id="restaurant_category_id" required
                                    class="form-select js-select2" data-control="select2"
                                    data-placeholder="Select An Option">
                                    <option value="" disabled>Select a Option</option>
                                    @foreach ($restaurantCategories as $restaurantCategory)
                                        <option value="{{ $restaurantCategory->id }}"
                                            @if ($restaurantCategory->id == $banner->restaurant_category_id) selected @endif>
                                            {{ $restaurantCategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-1">
                            <label for="formControlSelect" class="  form-label required">
                                Type:
                            </label>
                            <div>
                                <select class="form-select js-select2" data-control="select2" id="type" name="type"
                                    data-placeholder="Select an option" onchange="changeView();" required>
                                    <option value="" disabled>Select a type</option>
                                    <option value="MULTI_RESTAURANT" @if ($banner->type == 'MULTI_RESTAURANT') selected @endif>
                                        Restaurants </option>
                                    <option value="ITEM" @if ($banner->type == 'ITEM') selected @endif>
                                        Item</option>
                                </select>
                            </div>
                        </div>
                        <div id="restaurants"
                            @if ($banner->type == 'MULTI_RESTAURANT') style="display: show" @else style="display: none" @endif
                            class="col-lg-12 mb-1">
                            <label for="restaurant_ids" class=" form-label">Banner Applicable Restaurants
                                :</label>
                            <div>
                                <select id="multi_restaurant" name="restaurant_ids[]" id="restaurant_ids" multiple
                                    class="form-select js-select2" data-control="select2"
                                    data-placeholder="Select an option">
                                    @foreach ($restaurants as $restaurant)
                                        <option value="{{ $restaurant->id }}"
                                            class="text-capitalize multi_restaurant_selectbox"
                                            @if (isset($banner) && in_array($restaurant->id, $bannerRestaurants)) selected @endif>
                                            {{ $restaurant->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="single_restaurant"
                            @if ($banner->type == 'SINGLE_RESTAURANT') style="display: show" @else style="display: none" @endif
                            class="col-lg-12 mb-1">
                            <label for="restaurant_id" class="form-label">Banner Single Restaurant:</label>
                            <div>
                                <select class="form-select js-select2" data-control="select2" name="restaurant_id"
                                    data-placeholder="Select an option" id="restaurant_id">
                                    @foreach ($restaurants as $restaurant)
                                        <option value="{{ $restaurant->id }}"
                                            class="text-capitalize restaurant_selectbox"
                                            @if (isset($banner) && in_array($restaurant->id, $bannerRestaurants)) selected @endif>
                                            {{ $restaurant->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="items"
                            @if ($banner->type == 'ITEM') style="display: show" @else style="display: none" @endif>
                            <div class=" row">
                                <label class="form-label">Banner Applicable Items:</label>
                                <div>
                                    <select class="form-select js-select2" data-control="select2" id="item_id"
                                        name="item_id[]" multiple data-placeholder="Select An option">
                                        @foreach ($items as $item)
                                            <option value="{{ $item->id }}" class="text-capitalize items_selectbox"
                                                @if (isset($banner) && in_array($item->id, $bannerItems)) selected @endif>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>
                        <script>
                            function changeView() {
                                var type = document.getElementById("type").value;
                                if (type == "MULTI_RESTAURANT") {
                                    $('#restaurants').show();
                                    $('#single_restaurant').hide();
                                    $('#items').hide();
                                } else if (type == "SINGLE_RESTAURANT") {
                                    $('#restaurants').hide();
                                    $('#single_restaurant').show();
                                    $('#items').hide();
                                } else if (type == "ITEM") {
                                    $('#items').show();
                                    $('#restaurants').hide();
                                    $('#single_restaurant').hide();
                                }
                            }
                        </script>
                        <div class="mt-3 text-end">
                            <button type="submit" id="kt_modal_add_customer_submit" class="btn btn-success">
                                Update
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $(document).on('change', '#restaurant_category_id , #city_id  , #type', function() {
                if ($('#type').val() == "ITEM") {
                    $.ajax({
                        type: "get",
                        url: '{{ url('/get-restaurant-category-items') }}',
                        data: {
                            restaurant_category_id: $('#restaurant_category_id').val(),
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
                } else if ($('#type').val() == "SINGLE_RESTAURANT") {
                    $.ajax({
                        type: "get",
                        url: '{{ url('/get-restaurant-category-restaurants') }}',
                        data: {
                            restaurant_category_id: $('#restaurant_category_id').val(),
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
                } else if ($('#type').val() == "MULTI_RESTAURANT") {
                    $.ajax({
                        type: "get",
                        url: '{{ url('/get-restaurant-category-restaurant') }}',
                        data: {
                            restaurant_category_id: $('#restaurant_category_id').val(),
                            city_id: $('#city_id').val(),
                        },
                        success: function(data) {
                            console.log(data)
                            $('.multi_restaurant_selectbox').remove();
                            $.each(data, function(key, item) {
                                document.getElementById('multi_restaurant').innerHTML +=
                                    '<option class="multi_restaurant_selectbox" value=' +
                                    item.id +
                                    '>' + item.name + '</option>'
                            });
                        }

                    })
                }
            });
        });
    </script>
@endsection
