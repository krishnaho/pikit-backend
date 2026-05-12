@extends('admin.layout.master')
@section('title')
    Edit Restaurant
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content d-flex justify-content-between w-100">
                    <h4 class="nk-block-title page-title">Edit Restaurant <span class="badge bg-purple">
                            {{ $restaurant->name }}</span></h4>
                
                    @if (empty($restaurant->howin_fleet_team_id))
                        <a href="{{route('admin.syncTeamToFeet', $restaurant->id)}}" class="btn btn-info">Sync Fleet Team</a> 
                    @endif
                </div>
            </div>
        </div>
        <div class="card">

            <form action="{{ route('admin.updateRestaurant') }}" method="POST" enctype="multipart/form-data" id="myForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name='id' value="{{ $restaurant->id }}" />
                    <div class='form-group row'>
                        <div class="col-lg-6  mb-1">
                            <label for="name" class="required form-label">Name</label>
                            <input type="text" value="{{ $restaurant->name }}" class="form-control" placeholder="Name"
                                name='name' id='name'  required/>
                        </div>
                        <div class="col-lg-6  mb-1">
                            <label for="image" class=" form-label">Image</label>
                            <input type="file" class="form-control" placeholder="Image" name='image' id="image" />
                        </div>
                        <label class=" col-lg-3  form-label">Current Image</label>
                        <div class="col-lg-9">
                            @if ($restaurant->image)
                                <img src="{{ asset($restaurant->image) }}"
                                    style=" height:6rem;object-fit:cover;border-radius:5px; " />
                            @else
                                <span class="badge badge-dot bg-info">No Image Found</span>
                            @endif
                        </div>
                        <div class="col-lg-6  mb-1">
                            <label for='description' class=" form-label">Description</label>
                            <input type="text" value="{{ $restaurant->description }}" class="form-control"
                                placeholder="Description" name='description' id='description'  />
                        </div>
                        <div class="col-lg-6  mb-1">
                            <label for="phone" class=" form-label">Phone</label>
                            <input type="tel" value="{{ $restaurant->phone }}" class="form-control" placeholder="Phone"
                                name='phone' name='phone'  />
                        </div>
                        <div class="col-lg-6  mb-1">
                            <label for='restaurant_category_id' class=" form-label">Restaurant
                                Category</label>
                            <select class="form-select js-select2" data-control="select2"
                                data-placeholder="Select an option" id="restaurant_category_id"
                                name='restaurant_category_id' >
                                <option value="" disabled selected>Select An Option</option>
                                @foreach ($restaurantCategories as $restaurantCategory)
                                    <option value="{{ $restaurantCategory->id }}"
                                        @if ($restaurant->restaurant_category_id == $restaurantCategory->id) selected @endif>
                                        {{ $restaurantCategory->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6  mb-1">
                            <label for='city_id' class=" form-label">City</label>
                            <select class="form-select js-select2" data-control="select2"
                                data-placeholder="Select an option" id="city_id" name='city_id'>
                                <option value="" disabled selected>Select An Option</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}" @if ($restaurant->city_id == $city->id) selected @endif>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6  mb-1">
                            <label for='address' class=" form-label">Address</label>
                            <input type="text" value="{{ $restaurant->address }}" class="form-control"
                                placeholder="Address" name='address' id='address'  />
                        </div>
                        <div class="col-lg-6  mb-1">
                            <label for='land_mark' class="  form-label">Landmark</label>
                            <input type="text" value="{{ $restaurant->land_mark }}" class="form-control"
                                placeholder="Landmark" name='land_mark' id='land_mark'  />
                        </div>

                        <div class="col-lg-6  mb-1">
                            <label for='howin_fleet_taam_id' class=" form-label">Howin Fleet Taam
                                ID</label>
                            <input type="text" value="{{ $restaurant->howin_fleet_team_id }}" class="form-control" placeholder="Howin Fleet Taam ID"
                                name='howin_fleet_team_id' id='howin_fleet_taam_id' readonly />
                        </div>


                        <div class="col-lg-6  mb-1">
                            <label for='latitude' class=" form-label">Latitude</label>
                            <input type="number" value="{{ $restaurant->latitude }}" step="any"
                                class="form-control" placeholder="Latitude" id='latitude' name="latitude"  />
                        </div>
                        <div class="col-lg-6  mb-1">
                            <label for='longitude' class="  form-label">Longitude</label>
                            <input type="number" value="{{ $restaurant->longitude }}" step="any"
                                class="form-control" placeholder="Longitude" id='longitude' name="longitude"  />
                        </div>
                        <div class="col-lg-6  mb-1">
                            <label for='delivery_radius' class="  form-label">Radius</label>
                            <input type="text" value="{{ $restaurant->delivery_radius }}" class="form-control"
                                placeholder="Radius" name='delivery_radius' id='delivery_radius'  />
                        </div>
                        <div class="col-lg-6  mb-1">
                            <label for='rating' class="  form-label">Rating</label>
                            <input type="text" class="form-control" value="{{ $restaurant->rating }}"
                                placeholder="Rating" name='rating' id='rating'  />
                        </div>
                        <div class="col-lg-6  mb-1">
                            <label for='restaurant_charges' class="  form-label">Restaurant Charge</label>
                            <input type="number" value="{{ $restaurant->restaurant_charges }}" step="any"
                                class="form-control" placeholder="Restaurant Charge" name='restaurant_charges'
                                id='restaurant_charges'  />
                        </div>
                        <div class="col-lg-6  mb-1">
                            <label for='min_order_price' class="  form-label">Min Order Price</label>
                            <input type="number" value="{{ $restaurant->min_order_price }}" step="any"
                                class="form-control" placeholder="Min Order Price" name='min_order_price'
                                id='min_order_price'  />
                        </div>
                        <div class="col-lg-6  mb-1">
                            <label for='commission_rate' class="  form-label">Commission</label>
                            <input type="number" value="{{ $restaurant->commission_rate }}" step="any"
                                class="form-control" placeholder="Commission" name='commission_rate'
                                id='commission_rate'  />
                        </div>
                        <div class="col-lg-6  mb-1">
                            <label for='tax' class="  form-label">Tax</label>
                            <input type="number" value="{{ $restaurant->tax }}" step="any" class="form-control"
                                placeholder="Tax" name='tax' id='tax'  />
                        </div>
                        <div class="col-lg-6  mb-1">
                            <label for='approx_time_delivery' class="  form-label">Aprox Delivery time</label>
                            <input type="text" value="{{ $restaurant->approx_time_delivery }}" step="any" class="form-control"
                                placeholder="Tax" name='approx_time_delivery' id='approx_time_delivery'  />
                        </div>
                        <div class="col-lg-6  mb-1">
                            <label for='offer_text1' class="  form-label">Offer Text one</label>
                            <input type="text" value="{{ $restaurant->offer_text1 }}" step="any" class="form-control"
                                placeholder="Offer Text one" name='offer_text1' id='offer_text1'  />
                        </div>
                        <div class="col-lg-6  mb-1">
                            <label for='offer_text2' class="  form-label">Offer Text two</label>
                            <input type="text" value="{{ $restaurant->offer_text2 }}" step="any" class="form-control"
                                placeholder="Offer Text two" name='offer_text2' id='offer_text2'  />
                        </div>
                      
                    
                        <div class="col-lg-12  mt-4">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="is_popular"
                                    @if ($restaurant->is_popular) checked @endif id="is_popular">
                                <label class="custom-control-label" for="is_popular">Is Popular</label>
                            </div>
                        </div>
                        <div class="col-lg-12  mt-4">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="is_recommended"
                                    @if ($restaurant->is_recommended) checked @endif id="is_recommended">
                                <label class="custom-control-label" for="is_recommended">Is Recommended</label>
                            </div>
                        </div>
                        <div class="col-lg-12  mt-4">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="is_veg"
                                    @if ($restaurant->is_veg) checked @endif id="is_veg">
                                <label class="custom-control-label" for="is_veg">Is Veg</label>
                            </div>
                        </div>
                        <div class="col-lg-12  mt-4">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="is_freedelivery"
                                    @if ($restaurant->is_freedelivery) checked @endif id="is_freedelivery">
                                <label class="custom-control-label" for="is_freedelivery">Is Freedelivery</label>
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

@endsection
