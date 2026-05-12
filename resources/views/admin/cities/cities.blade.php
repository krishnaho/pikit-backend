@extends('admin.layout.master')
@section('title')
    Cities
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">All Cities</h4>
                </div>
                <div class="nk-block-head-content">
                    <a data-bs-toggle="modal" data-bs-target="#add_city" class="btn btn-primary">
                        <em class="icon ni ni-plus"></em>
                        <span>Add City</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="table card-bordered" id="cities" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="col">Id</th>
                                <th class="col">Name</th>
                                <th class="col">Latitude</th>
                                <th class="col">Longitude</th>
                                <th class="col">Status</th>
                                <th class="col">City User</th>
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

    @foreach ($cities as $city)
        <div class="modal fade" id="deleteCity{{ $city->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete City</h5>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </div>
                    </div>
                    <div class="modal-body">
                        <p>Do you really want to delete this City ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('admin.deleteCity', $city->id) }}" type="button"
                            class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="cityUserModel{{ $city->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-center modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">City Users</h5>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </div>
                    </div>
                    <form action="{{ route('admin.updateCityUser') }}" method="get">
                        <div class="modal-body">
                            <input type="hidden" name="id" value="{{ $city->id }}">
                            <div class="form-group  row mt-2" style="align-items: center">
                                <label class="form-label col-lg-3"> Add City Users</label>
                                <div class="col-lg-9 form-control-wrap">
                                    @php
                                        $userIds = $city->users->pluck('id')->toArray();
                                    @endphp
                                    <select class="form-select js-select2" data-placeholder="Select City Users"
                                        aria-placeholder="Select City Users" name="city_users[]" id="city_users" multiple>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                @if (in_array($user->id, $userIds)) selected @endif>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-info"><i class=" mr-1"></i> Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editCity{{ $city->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-top modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update City</h5>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </div>
                    </div>
                    <form action="{{ route('admin.updateCity') }}" method="POST" enctype="multipart/form-data"
                        id="myForm">
                        @csrf
                        <div class="modal-body ">
                            <input type="hidden" name='id' value="{{ $city->id }}" required />
                            <div class='form-group row mb-4 align-middle'>
                                <label class=" col-lg-3 required form-label">Name</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" placeholder="Name" name='name'
                                        value="{{ $city->name }}" required />
                                </div>
                            </div>
                            <div class='form-group row mb-4'>
                                <label class=" col-lg-3 required form-label">Latitude</label>
                                <div class="col-lg-9">
                                    <input type="number" step="any" class="form-control" placeholder="Latitude"
                                        value="{{ $city->latitude }}" name="latitude" required />
                                </div>
                            </div>
                            <div class='form-group row mb-4'>
                                <label class=" col-lg-3 required form-label">Longitude</label>
                                <div class="col-lg-9">
                                    <input type="number" step="any" class="form-control" placeholder="Longitude"
                                        value="{{ $city->longitude }}" name="longitude" required />
                                </div>
                            </div>
                            <div class='form-group row mb-4'>
                                <label class=" col-lg-3 required form-label">Radius</label>
                                <div class="col-lg-9">
                                    <input type="number" class="form-control" placeholder="Radius" name="radius"
                                        value="{{ $city->radius }}" required />
                                </div>
                            </div>
                            <div class='form-group row mb-4'>
                                <label class=" col-lg-3 form-label">Delivery Charge Type</label>
                                <div class="col-lg-9">
                                    <select class="form-select js-select2" data-control="select2"
                                        data-placeholder="Select an option"
                                        id="delivery_charge_type_update_{{ $city->id }}" name='delivery_charge_type'
                                        required>
                                        <option value="FIXED" @if ($city->delivery_charge_type == 'FIXED') selected @endif>Fixed
                                            Amount</option>
                                        <option value="DYNAMIC" @if ($city->delivery_charge_type == 'DYNAMIC') selected @endif>Dynamic
                                            Amount</option>
                                    </select>
                                </div>
                            </div>
                            <div class='form-group fixed_amount_edit_{{ $city->id }} row mb-4' id=""
                                @if ($city->delivery_charge_type == 'DYNAMIC') style="display:none" @endif>
                                <label class=" col-lg-3 required form-label">Delivery Charge</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" placeholder="Delivery Charge"
                                        value="{{ $city->delivery_charge }}" name="delivery_charge" required />
                                </div>
                            </div>
                            <div class="dynamic_amount_edit_{{ $city->id }}"
                                @if ($city->delivery_charge_type == 'FIXED') style="display:none" @endif>
                                <div class='form-group row mb-4'>
                                    <label class=" col-lg-3 align-items-center required form-label">Base Delivery
                                        Charge</label>
                                    <div class="col-lg-3">
                                        <input type="number" class="form-control" placeholder="Base Delivery Charge"
                                            value="{{ $city->base_delivery_charge }}" name="base_delivery_charge"
                                            id="base_delivery_charge" />
                                    </div>

                                    <label class=" col-lg-3 required form-label">Base Delivery Distance</label>
                                    <div class="col-lg-3">
                                        <input type="number" class="form-control" placeholder="Base Delivery Distance"
                                            value="{{ $city->base_delivery_distance }}" name="base_delivery_distance"
                                            id="base_delivery_distance" />
                                    </div>

                                    <label class=" col-lg-3 required form-label">Extra Delivery Charge</label>
                                    <div class="col-lg-3">
                                        <input type="number" class="form-control" placeholder="Extra Delivery Charge"
                                            value="{{ $city->extra_delivery_charge }}" name="extra_delivery_charge"
                                            id="extra_delivery_charge" />
                                    </div>

                                    <label class=" col-lg-3 required form-label">Extra Delivery Distance</label>
                                    <div class="col-lg-3">
                                        <input type="number" class="form-control" placeholder="Extra Delivery Distance"
                                            value="{{ $city->extra_delivery_distance }}" name="extra_delivery_distance"
                                            id="extra_delivery_distance" />
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

        <script>
            $(document).ready(function() {
                $(document).on('change', '#delivery_charge_type_update_{{ $city->id }}', function() {
                    var type = $(this).val();

                    if (type == "DYNAMIC") {
                        $('.dynamic_amount_edit_{{ $city->id }}').show();
                        $('.fixed_amount_edit_{{ $city->id }}').hide();
                    } else {
                        $('.dynamic_amount_edit_{{ $city->id }}').hide();
                        $('.fixed_amount_edit_{{ $city->id }}').show();
                    }

                });
            });
        </script>
    @endforeach

    <div class="modal fade" id="add_city" tabindex="-1">
        <div class="modal-dialog modal-dialog-top modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add City</h5>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </div>
                </div>
                <form action="{{ route('admin.addCity') }}" method="POST" enctype="multipart/form-data"
                    id="myForm">
                    @csrf
                    <div class="modal-body ">
                        <div class='form-group row mb-4 align-middle'>
                            <label class=" col-lg-3 required form-label">Name</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" placeholder="User Name" name='name'
                                    required />
                            </div>
                        </div>
                        <div class='form-group row mb-4'>
                            <label class=" col-lg-3 required form-label">Latitude</label>
                            <div class="col-lg-9">
                                <input type="number" step="any" class="form-control" placeholder="Latitude"
                                    name="latitude" required />
                            </div>
                        </div>
                        <div class='form-group row mb-4'>
                            <label class=" col-lg-3 required form-label">Longitude</label>
                            <div class="col-lg-9">
                                <input type="number" step="any" class="form-control" placeholder="Longitude"
                                    name="longitude" required />
                            </div>
                        </div>
                        <div class='form-group row mb-4'>
                            <label class=" col-lg-3 required form-label">Radius</label>
                            <div class="col-lg-9">
                                <input type="number" class="form-control" placeholder="Radius" name="radius"
                                    required />
                            </div>
                        </div>
                        <div class='form-group row mb-4'>
                            <label class=" col-lg-3 form-label">Delivery Charge Type</label>
                            <div class="col-lg-9">
                                <select class="form-select js-select2" data-control="select2"
                                    data-placeholder="Select an option" id="delivery_charge_type"
                                    name='delivery_charge_type'>
                                    <option value="FIXED">Fixed Amount</option>
                                    <option value="DYNAMIC">Dynamic Amount</option>
                                </select>
                            </div>
                        </div>
                        <div class='form-group row mb-4' id="fixed_amount_add">
                            <label class=" col-lg-3 required form-label">Delivery Charge</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" placeholder="Delivery Charge"
                                    name="delivery_charge" id="delivery_charge" required />
                            </div>
                        </div>
                        <div id="dynamic_amount_add" style="display: none;">
                            <div class='form-group row mb-4'>
                                <label class=" col-lg-3 align-items-center required form-label">Base Delivery
                                    Charge</label>
                                <div class="col-lg-3">
                                    <input type="number" class="form-control" placeholder="Base Delivery Charge"
                                        name="base_delivery_charge" id="base_delivery_charge" />
                                </div>

                                <label class=" col-lg-3 required form-label">Base Delivery Distance</label>
                                <div class="col-lg-3">
                                    <input type="number" class="form-control" placeholder="Base Delivery Distance"
                                        name="base_delivery_distance" id="base_delivery_distance" />
                                </div>

                                <label class=" col-lg-3 required form-label">Extra Delivery Charge</label>
                                <div class="col-lg-3">
                                    <input type="number" class="form-control" placeholder="Extra Delivery Charge"
                                        name="extra_delivery_charge" id="extra_delivery_charge" />
                                </div>

                                <label class=" col-lg-3 required form-label">Extra Delivery Distance</label>
                                <div class="col-lg-3">
                                    <input type="number" class="form-control" placeholder="Extra Delivery Distance"
                                        name="extra_delivery_distance" id="extra_delivery_distance" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {

            $(document).on('change', '#delivery_charge_type', function() {
                var type = $(this).val();
                if (type == "DYNAMIC") {
                    $('#dynamic_amount_add').show();
                    $('#fixed_amount_add').hide();
                } else {
                    $('#dynamic_amount_add').hide();
                    $('#fixed_amount_add').show();
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            var datatable = $('#cities').DataTable({
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
                ajax: '{{ route('admin.getAllCities') }}',
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name',
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
                        data: 'status',
                        sortable: false
                    },
                    {
                        data: 'city_user',
                        sortable: false
                    },
                    {
                        data: 'action',
                        sortable: false
                    },
                ],

            });
            var search = document.querySelector('.search');
            html = search?.innerHTML
            if (html) {
                html.innerHTML = '<span class="fw-3 px-2">Show</span>' + html +
                    '<span class="fw-3 px-2">Entries</span>'
            }
        });
    </script>
@endsection
