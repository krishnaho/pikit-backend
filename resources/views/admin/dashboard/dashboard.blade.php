@extends('admin.layout.master')
@section('title')
    Dashboard
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">Dashboard</h4>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="row g-gs">
                <div class="col-xxl-4 col-md-4">
                    <div class="card h-100">
                        <div class="nk-ecwg nk-ecwg3">
                            <div class="card-inner">
                                <div class="card-title-group">
                                    <div class="card-title">
                                        <h6 class="title">Total Users</h6>
                                    </div>
                                    <div>
                                        <em class="icon ni ni-user-alt-fill" style="font-size: 30px"></em>
                                    </div>
                                </div>
                                <div class="data">
                                    <div class="amount">{{ $totalUserCount }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-md-4">
                    <div class="card h-100">
                        <div class="nk-ecwg nk-ecwg3">
                            <div class="card-inner">
                                <div class="card-title-group">
                                    <div class="card-title">
                                        <h6 class="title">Total Sales</h6>
                                    </div>
                                    <div>
                                        <em class="icon ni ni-bag-fill" style="font-size: 30px"></em>
                                    </div>
                                </div>
                                <div class="data">
                                    <div class="amount"> ₹ {{ $totalSales }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-md-4">
                    <div class="card h-100">
                        <div class="nk-ecwg nk-ecwg3">
                            <div class="card-inner">
                                <div class="card-title-group">
                                    <div class="card-title">
                                        <h6 class="title">Total Restaurant</h6>
                                    </div>
                                    <div>
                                        <em class="icon ni ni-home-alt" style="font-size: 30px"></em>
                                    </div>
                                </div>
                                <div class="data">
                                    <div class="amount"> {{ $totalRestaurants->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-12">
                    <div class="card card-full">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Recent Orders</h6>
                                </div>
                            </div>
                        </div>
                        <div class="nk-tb-list mt-n2">
                            <div class="nk-tb-item nk-tb-head">
                                <div class="nk-tb-col fw-bolder fs-6 text-muted"><span>Unique ID</span></div>
                                <div class="nk-tb-col tb-col-sm fw-bolder fs-6 text-muted"><span>Customer Name</span></div>
                                <div class="nk-tb-col tb-col-sm fw-bolder fs-6 text-muted"><span>Restaurant Name</span>
                                </div>
                                <div class="nk-tb-col tb-col-md fw-bolder fs-6 text-muted"><span>Total</span></div>
                                <div class="nk-tb-col tb-col-sm fw-bolder fs-6 text-muted"><span>Date</span></div>
                                <div class="nk-tb-col fw-bolder fs-6 text-muted"><span
                                        class="d-none d-sm-inline">Status</span></div>
                            </div>
                            @foreach ($orders as $order)
                                <div class="nk-tb-item">
                                    <div class="nk-tb-col"><span>{{ $order->unique_order_id }}</span></div>
                                    <div class="nk-tb-col"><span>{{ $order->user?->name }}</span></div>
                                    <div class="nk-tb-col"><span> {{ $order->restaurant->name ?? '' }}</span></div>
                                    <div class="nk-tb-col"><span> {{ $order->total }}</span></div>
                                    <div class="nk-tb-col"><span> {{ $order->created_at->diffForHumans() }}</span></div>
                                    <div class="nk-tb-col">
                                        @if (!$order->orderstatus)
                                            <span>--</span>
                                        @elseif ($order->orderstatus->name == 'Completed')
                                            <span class="badge badge-dot bg-success">Completed</span>
                                        @elseif ($order->orderstatus->name == 'Cancelled')
                                            <span class="badge badge-dot bg-danger">Cancelled</span>
                                        @else
                                            <span class="badge badge-dot bg-info">{{ $order->orderstatus->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
