@extends('admin.layout.master')
@section('title')
Restaurant Owner
@endsection
@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Manage Restaurant</h3>
                            </div>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner user-select-none">
                                <form action="{{ route('admin.updateManageRestaurant') }}" method="POST"
                                    enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="{{ $user->id }}">
                                    @csrf
                                    <div class='form-group row mb-4 align-middle'>
                                        <label class=" col-lg-6 required form-label">User:</label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control" placeholder="Restaurant Name"
                                                name='name' value="{{ $user->name }}" disabled />
                                        </div>
                                    </div>

                                    <div class='form-group row mb-4 align-middle'>
                                        <label class=" col-lg-6 required form-label">Restaurants:</label>
                                        <div class="col-lg-6">
                                            <select class="form-select js-select2" name="restaurants[]" multiple>
                                                @foreach ($restaurants as $restaurant)
                                                    <option value="{{ $restaurant->id }}"
                                                        @if (in_array($restaurant->id, $restaurantOwnerRestaurantIds)) selected @endif>
                                                        {{ $restaurant->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class='form-group mb-4 text-end'>
                                        <button type="submit" class="btn btn-info ml-2 mb-2">
                                            Update<i class="fe fe-database ml-2"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#manageRestaurants').click(function() {
            $("#manageStoreDiv").toggle();
        });


        var clicked = false;
        $("#selectAllStores").on("click", function() {

            $(".inputCheck").prop("checked", !clicked);
            clicked = !clicked;
            this.innerHTML = clicked ? 'Deselect All' : 'Select All';

        });
    </script>
@endsection
