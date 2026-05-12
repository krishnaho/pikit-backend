@extends('admin.layout.master')
@section('title')
    Report
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">Restaurant Payout Report</h4>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <form action="">
                        @csrf
                        <div class="modal-body ">
                            <div class='form-group row mb-2'>
                                <div class="col-lg-6 ">
                                    <label for="dates" class="required form-label">Datepicker Range</label>
                                    <div class="input-daterange date-picker-range input-group" id="dates">
                                        <div class="input-group-addon">From</div>
                                        <input type="text" class="form-control" name="start_date" autocomplete="off"
                                            @if (isset($start_date)) value="{{ $start_date }}" @endif required />
                                        <div class="input-group-addon">To</div>
                                        <input type="text" class="form-control" name="end_date" autocomplete="off"
                                            @if (isset($end_date)) value="{{ $end_date }}" @endif required />
                                    </div>
                                </div>
                                <div class="col-lg-6 d-flex justify-content-end align-items-end">
                                    <div class="me-4 col-lg-1 text-bottom">
                                        <button type="submit" class="btn btn-success">Filter</button>
                                    </div>
                                    @csrf
                                    <input type='hidden' value='COMPLETED' name='type' />
                                    <div class="col-lg-1 ms-4">
                                        <button type="submit" formaction="{{ route('restaurantOwner.exportRestaurantPayout') }}"
                                            formmethod='POST' class="btn btn-success">Export </button>
                                    </div>
                                    <div style=" margin-left: 57px; ">
                                        <a href="{{ route('restaurantOwner.viewRestaurantPayoutReport')}}" class="btn btn-danger" >Pending Payouts</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    

                    <table class="table card-bordered mt-4" id="payouts" style="width: 100%">
                        <thead>
                            <tr class="">
                                <th>Restaurant</th>
                                <th>Total Amount</th>
                                <th>Tax Amount</th>
                                <th>Restaurant Charge</th>
                                <th>Total Deductions</th>
                                <th>Restaurant Payout Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            @foreach ($restaurants as $restaurant)
                                <tr class="text-center">
                                    <td>
                                        {{ $restaurant->name }}
                                    </td>
                                    <td>{{ $restaurant->orders->sum('sub_total') }}
                                    </td>
                                    <td>
                                        {{ $restaurant->orders->sum('tax') }}
                                    </td>
                                    <td>{{ $restaurant->orders->sum('restaurant_charges') }}</td>
                                    <td>{{ $restaurant->orders->sum('total_commission') }}</td>
                                    <td>{{ $restaurant->orders->sum('payout_amount') }}</td>
                               
                                    <td><span class="badge bg-success">COMPLETED</span></td>
                                   
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @foreach ($restaurants as $restaurant)
        <div class="modal fade" tabindex="-1" id="viewOrders{{ $restaurant->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">View orders</h5>
                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <span class="svg-icon svg-icon-2x" style="font-size: 16px">&times;</span>
                        </div>
                        <!--end::Close-->
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table card-bordered" id="payouts" style="width: 100%">
                                <thead>
                                    <tr class=" fw-bolder fs-7 text-uppercase gs-0">
                                        <th>Unique order Id</th>
                                        <th>Status</th>
                                        <th>Restaurant</th>
                                        <th>Total</th>
                                        <th>Created At</th>
                                       
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-bold">
                                    @foreach ($restaurant->orders as $order)
                                        <tr>
                                            <td>{{ $order->unique_order_id }}</td>
                                            
                                            <td>
                                                @if ($order->is_payout_released == 1)
                                                    <span class="badge badge-success">Completed</span>
                                                @else
                                                    <span class="badge badge-warning">Pending </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($order->restaurant->name)
                                                    {{ $order->restaurant->name }}
                                                @else
                                                    No Restaurant Found
                                                @endif
                                            </td>
                                            <td>{{ $order->payout_amount }}</td>
                                            <td>{{ $order->created_at->diffForHumans() }}</td>
                                          
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                           
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection
