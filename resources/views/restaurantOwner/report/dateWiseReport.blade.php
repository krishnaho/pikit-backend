@extends('admin.layout.master')
@section('title')
    Report
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">Date Wise Sales</h4>
                </div>
            </div>
        </div>

        <div class="nk-block">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <form action="">
                        @csrf
                        <div class="modal-body ">
                            <div class='form-group row mb-4'>
                                <div class="col-lg-6 mb-2">
                                    <label for="dates" class="required form-label">Datepicker Range</label>
                                    <div class="input-daterange date-picker-range input-group" id="dates">
                                        <div class="input-group-addon">From</div>
                                        <input type="text" class="form-control" name="start_date" autocomplete="off"
                                            required />
                                        <div class="input-group-addon">To</div>
                                        <input type="text" class="form-control" name="end_date" autocomplete="off"
                                            required />
                                    </div>
                                </div>

                                <div class="col-lg-6 mb-2">
                                    <label for="status" class="required form-label">Choose Status</label>
                                    <select class="form-select js-select2" name="status" id="status"
                                        data-placeholder="Select Status" required>
                                        <option value="ALL">ALL</option>
                                        <option value="COMPLETED">COMPLETED</option>
                                        <option value="CANCELLED">CANCELLED</option>
                                    </select>
                                </div>
                                <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary"
                                formaction="{{ route('restaurantOwner.exportDateWise') }}">Export</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
