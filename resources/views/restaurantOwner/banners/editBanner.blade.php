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
            <form class="form" method="POST" action="{{ route('restaurantOwner.updateBanner') }}"
                enctype="multipart/form-data">
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
                        <input type="hidden" name='restaurant_id' value="{{ $restaurant->id }}" />
                        <input type="hidden" name='type' value="ITEM" />
                        <div >
                            <div class=" row">
                                <label class="form-label">Banner Applicable Items:</label>
                                <div>
                                    <select class="form-select js-select2" data-control="select2" id="item_id"
                                        name="item_id[]" multiple data-placeholder="Select An option">
                                        <option value="" disabled>Select An option</option>
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
@endsection
