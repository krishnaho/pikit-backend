@extends('admin.layout.master')
@section('title')
    Settings
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">Settings</h4>
                </div>
            </div>
        </div>

        <div class="nk-block">
            <div class="card card-bordered card-preview">
                <div class="card-inner">

                    <h5>Home Page</h5>

                    <form action="{{ route('admin.todayGoldRate') }}" method="post">
                        @csrf
                        <input type="hidden" name="key" value="platform_fee">
                        <div class="form-group row mt-3">
                            <label class="col-form-label col-lg-4">Platform fee</label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control"
                                    name="platform_fee"
                                    @if ($platFormfee !== null) value="{{ $platFormfee->value }}" @endif
                                    placeholder="Enter Platformfee">
                            </div>
                            <div class="col-lg-2">
                                <button type="submit" class="btn btn-success">Update</button>
                            </div>
                        </div>
                    </form>

                   

                </div>
            </div>
        </div>



    </div>
@endsection
