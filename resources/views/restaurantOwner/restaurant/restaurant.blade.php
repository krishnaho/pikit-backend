@extends('admin.layout.master')
@section('title')
    Restaurant
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">Restaurants</h4>
                </div>
                <div class="nk-block-head-content">
                    <a data-bs-toggle="modal" data-bs-target="#add_restaurant" class="btn btn-primary">
                        <em class="icon ni ni-plus"></em>
                        <span>Add Restaurant</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="table card-bordered" id="restaurant" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="col">Id</th>
                                <th class="col">Name</th>
                                <th class="col">Restaurant Category</th>
                                <th class="col">Image</th>
                                <th class="col">Phone</th>
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

    @foreach ($restaurants as $restaurant)
        <div class="modal fade" id="deleteRestaurant{{ $restaurant->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Restaurant</h5>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </div>
                    </div>
                    <div class="modal-body">
                        <p>Do you really want to delete this Restaurant ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('admin.deleteRestaurant', $restaurant->id) }}" type="button"
                            class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="editRestaurant{{ $restaurant->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-top modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Restaurant</h5>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </div>
                    </div>
                    <form action="{{ route('admin.updateRestaurant') }}" method="POST" enctype="multipart/form-data"
                        id="myForm">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name='id' value="{{ $restaurant->id }}" />
                            <div class='form-group row'>
                                <div class="col-lg-6  mb-1">
                                    <label for="name" class="required form-label">Name</label>
                                    <input type="text" value="{{ $restaurant->name }}" class="form-control"
                                        placeholder="Name" name='name' id='name' required />
                                </div>
                                <div class="col-lg-6  mb-1">
                                    <label for="image" class="required form-label">Image</label>
                                    <input type="file" class="form-control" placeholder="Image" name='image'
                                        id="image" />
                                </div>
                                <label class=" col-lg-3 required form-label">Current Image</label>
                                <div class="col-lg-9">
                                    @if ($restaurant->image)
                                        <img src="{{ asset($restaurant->image) }}"
                                            style=" height:6rem;object-fit:cover;border-radius:5px; " />
                                    @else
                                        <span class="badge badge-dot bg-info">No Image Found</span>
                                    @endif
                                </div>
                                <div class="col-lg-6  mb-1">
                                    <label for='description' class="required form-label">Description</label>
                                    <input type="text" value="{{ $restaurant->description }}" class="form-control"
                                        placeholder="Description" name='description' id='description' required />
                                </div>
                                <div class="col-lg-6  mb-1">
                                    <label for="phone"
                                        class="required form-label">Phone</label>
                                    <input type="tel" value="{{ $restaurant->phone }}" class="form-control" placeholder="Phone" name='phone'
                                        name='phone' required />
                                </div>
                                <div class="col-lg-6  mb-1">
                                    <label for='restaurant_category_id' class="required form-label">Restaurant
                                        Category</label>
                                    <select class="form-select js-select2" data-control="select2"
                                        data-placeholder="Select an option" id="restaurant_category_id"
                                        name='restaurant_category_id' data-dropdown-parent="#editRestaurant{{ $restaurant->id }}"
                                        data-allow-clear="true">
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
                                    <label for='city_id' class="required form-label">City</label>
                                    <select class="form-select js-select2" data-control="select2"
                                        data-placeholder="Select an option" id="city_id" name='city_id'
                                        data-dropdown-parent="#editRestaurant{{ $restaurant->id }}" data-allow-clear="true">
                                        <option value="" disabled selected>Select An Option</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}"
                                                @if ($restaurant->city_id == $city->id) selected @endif>
                                                {{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6  mb-1">
                                    <label for='address' class="required form-label">Address</label>
                                    <input type="text" value="{{ $restaurant->address }}" class="form-control"
                                        placeholder="Address" name='address' id='address' required />
                                </div>
                                <div class="col-lg-6  mb-1">
                                    <label for='land_mark' class=" required form-label">Landmark</label>
                                    <input type="text" value="{{ $restaurant->land_mark }}" class="form-control"
                                        placeholder="Landmark" name='land_mark' id='land_mark' required />
                                </div>
                                <div class="col-lg-6  mb-1">
                                    <label for='latitude' class="required form-label">Latitude</label>
                                    <input type="number" value="{{ $restaurant->latitude }}" step="any"
                                        class="form-control" placeholder="Latitude" id='latitude' name="latitude"
                                        required />
                                </div>
                                <div class="col-lg-6  mb-1">
                                    <label for='longitude' class=" required form-label">Longitude</label>
                                    <input type="number" value="{{ $restaurant->longitude }}" step="any"
                                        class="form-control" placeholder="Longitude" id='longitude' name="longitude"
                                        required />
                                </div>
                                <div class="col-lg-6  mb-1">
                                    <label for='delivery_radius' class=" required form-label">Radius</label>
                                    <input type="text" value="{{ $restaurant->delivery_radius }}" class="form-control"
                                        placeholder="Radius" name='delivery_radius' id='delivery_radius' required />
                                </div>
                                <div class="col-lg-6  mb-1">
                                    <label for='rating' class=" required form-label">Rating</label>
                                    <input type="text" class="form-control" value="{{ $restaurant->rating }}"
                                        placeholder="Rating" name='rating' id='rating' required />
                                </div>
                                <div class="col-lg-6  mb-1">
                                    <label for='restaurant_charges' class=" required form-label">Restaurant Charge</label>
                                    <input type="number" value="{{ $restaurant->restaurant_charges }}" step="any"
                                        class="form-control" placeholder="Restaurant Charge" name='restaurant_charges'
                                        id='restaurant_charges' required />
                                </div>
                                <div class="col-lg-6  mb-1">
                                    <label for='min_order_price' class=" required form-label">Min Order Price</label>
                                    <input type="number" value="{{ $restaurant->min_order_price }}" step="any"
                                        class="form-control" placeholder="Min Order Price" name='min_order_price'
                                        id='min_order_price' required />
                                </div>
                                <div class="col-lg-6  mb-1">
                                    <label for='commission_rate' class=" required form-label">Commission</label>
                                    <input type="number" value="{{ $restaurant->commission_rate }}" step="any"
                                        class="form-control" placeholder="Commission" name='commission_rate'
                                        id='commission_rate' required />
                                </div>
                                <div class="col-lg-6  mb-1">
                                    <label for='tax' class=" required form-label">Tax</label>
                                    <input type="number" value="{{ $restaurant->tax }}" step="any"
                                        class="form-control" placeholder="Tax" name='tax' id='tax' required />
                                </div>
                                <div class="col-lg-12  mt-4">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="is_popular"
                                        @if ($restaurant->is_popular) checked  @endif id="is_popular">
                                        <label class="custom-control-label" for="is_popular">Is Popular</label>
                                    </div>
                                </div>
                                <div class="col-lg-12  mt-4">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="is_recommended"
                                            @if ($restaurant->is_recommended) checked  @endif id="is_recommended">
                                        <label class="custom-control-label" for="is_recommended">Is Recommended</label>
                                    </div>
                                </div>
                                <div class="col-lg-12  mt-4">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="is_veg"
                                        @if ($restaurant->is_veg) checked  @endif id="is_veg">
                                        <label class="custom-control-label" for="is_veg">Is Veg</label>
                                    </div>
                                </div>
                                <div class="col-lg-12  mt-4">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="is_freedelivery"
                                        @if ($restaurant->is_freedelivery) checked  @endif id="is_freedelivery">
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
        </div>
    @endforeach

    <div class="modal fade" id="add_restaurant" tabindex="-1">
        <div class="modal-dialog modal-dialog-top modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Restaurant</h5>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </div>
                </div>
                <form action="{{ route('admin.addRestaurant') }}" method="POST" enctype="multipart/form-data"
                    id="myForm">
                    @csrf
                    <div class="modal-body">
                        <div class='form-group row'>
                            <div class="col-lg-6  mb-1">
                                <label for="name" class="required form-label">Name</label>
                                <input type="text" class="form-control" placeholder="Name" name='name'
                                    id='name' required />
                            </div>
                            <div class="col-lg-6  mb-1">
                                <label for="image" class="required form-label">Image</label>
                                <input type="file" class="form-control" placeholder="Image" name='image'
                                    id="image" required />
                            </div>
                            <div class="col-lg-6  mb-1">
                                <label for='description' class="required form-label">Description</label>
                                <input type="text" class="form-control" placeholder="Description" name='description'
                                    id='description' required />
                            </div>
                            <div class="col-lg-6  mb-1">
                                <label for="phone" class="required form-label">Phone</label>
                                <input type="tel" class="form-control" placeholder="Phone" name='phone'
                                    name='phone' required />
                            </div>
                            <div class="col-lg-6  mb-1">
                                <label for='restaurant_category_id' class="required form-label">Restaurant
                                    Category</label>
                                <select class="form-select js-select2" data-control="select2"
                                    data-placeholder="Select an option" id="restaurant_category_id"
                                    name='restaurant_category_id' data-dropdown-parent="#add_restaurant"
                                    data-allow-clear="true">
                                    <option value="" disabled selected>Select An Option</option>
                                    @foreach ($restaurantCategories as $restaurantCategory)
                                        <option value="{{ $restaurantCategory->id }}">{{ $restaurantCategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6  mb-1">
                                <label for='city_id' class="required form-label">City</label>
                                <select class="form-select js-select2" data-control="select2"
                                    data-placeholder="Select an option" id="city_id" name='city_id'
                                    data-dropdown-parent="#add_restaurant" data-allow-clear="true">
                                    <option value="" disabled selected>Select An Option</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6  mb-1">
                                <label for='address' class="required form-label">Address</label>
                                <input type="text" class="form-control" placeholder="Address" name='address'
                                    id='address' required />
                            </div>
                            <div class="col-lg-6  mb-1">
                                <label for='land_mark' class=" required form-label">Landmark</label>
                                <input type="text" class="form-control" placeholder="Landmark" name='land_mark'
                                    id='land_mark' required />
                            </div>
                            <div class="col-lg-6  mb-1">
                                <label for='latitude' class="required form-label">Latitude</label>
                                <input type="number" step="any" class="form-control" placeholder="Latitude"
                                    id='latitude' name="latitude" required />
                            </div>
                            <div class="col-lg-6  mb-1">
                                <label for='longitude' class=" required form-label">Longitude</label>
                                <input type="number" step="any" class="form-control" placeholder="Longitude"
                                    id='longitude' name="longitude" required />
                            </div>
                            <div class="col-lg-6  mb-1">
                                <label for='delivery_radius' class=" required form-label">Radius</label>
                                <input type="text" class="form-control" placeholder="Radius" name='delivery_radius'
                                    id='delivery_radius' required />
                            </div>
                            <div class="col-lg-6  mb-1">
                                <label for='rating' class=" required form-label">Rating</label>
                                <input type="text" class="form-control" placeholder="Rating" name='rating'
                                    id='rating' required />
                            </div>
                            <div class="col-lg-6  mb-1">
                                <label for='restaurant_charges' class=" required form-label">Restaurant Charge</label>
                                <input type="number" step="any" class="form-control"
                                    placeholder="Restaurant Charge" name='restaurant_charges' id='restaurant_charges'
                                    required />
                            </div>
                            <div class="col-lg-6  mb-1">
                                <label for='min_order_price' class=" required form-label">Min Order Price</label>
                                <input type="number" step="any" class="form-control" placeholder="Min Order Price"
                                    name='min_order_price' id='min_order_price' required />
                            </div>
                            <div class="col-lg-6  mb-1">
                                <label for='commission_rate' class=" required form-label">Commission</label>
                                <input type="number" step="any" class="form-control" placeholder="Commission"
                                    name='commission_rate' id='commission_rate' required />
                            </div>
                            <div class="col-lg-6  mb-1">
                                <label for='tax' class=" required form-label">Tax</label>
                                <input type="number" step="any" class="form-control" placeholder="Tax"
                                    name='tax' id='tax' required />
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
                            <div class="col-lg-12  mt-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="is_freedelivery" id="is_freedelivery">
                                    <label class="custom-control-label" for="is_freedelivery">Is Freedelivery</label>
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
    <script>
        $(document).ready(function() {
            var datatable = $('#restaurant').DataTable({
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
                ajax: '{{ route('restaurantOwner.getAllRestaurants') }}',
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name',
                        sortable: true,
                    },
                    {
                        data: 'restaurantCategory',
                        sortable: false
                    },
                    {
                        data: 'image',
                        sortable: false
                    },
                    {
                        data: 'phone',
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
