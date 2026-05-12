@extends('admin.layout.master')
@section('title')
    Live Orders
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">Live Orders</h4>
                </div>
            </div>
        </div>
        <div class="row d-flex align-items-center align-items-center">
            <div class="col-lg-6" id="select-city">
                <div class=" d-flex align-items-center align-items-center">
                    <div style="white-space: nowrap" class="pe-3">Select City: </div>
                    <select name="city" id="city_id" class="form-select js-select2 " data-control="select2"
                        style="width: 100%" onchange="changeCity();">
                        <option value="All" @if ($city_id == 'All') selected @endif>All</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}" @if ($city_id == $city->id) selected @endif>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div id="searchedhead" style="display: none" class="mt-5">
            <div class="row">
                <div class="col-lg-3">
                    <h3>Searched Orders</h3>
                </div>
            </div>
        </div>
        <div id="all-buttons" class="my-5">
            <div class="row">
                <div class="col-lg-3 text-center" id="instanthead">
                    <h3>Instant Live Orders</h3>
                </div>
                <div class="col-lg-3 text-center" id="scheduledhead" style="display: none">
                    <h3>Scheduled Orders</h3>
                </div>
                @if ((new \Jenssegers\Agent\Agent())->isDesktop())
                    <div class="col-lg-9 text-end" id="instant-button">
                        <button type="button" class="btn btn-success btn-scale me-5" data-bs-toggle="modal"
                            data-bs-target="#completed">
                            Completed Orders
                        </button>
                        <button type="button" class="btn btn-danger btn-scale me-5" data-bs-toggle="modal"
                            data-bs-target="#cancelled">
                            Cancelled Orders
                        </button>
                        <span id="schedulecount">
                            <button type="button" class="btn btn-dark btn-scale me-5" style="white-space:nowrap"
                                onclick="viewScheduled();">
                                Scheduled Orders <span class="ms-2 badge badge-dark">{{ $schedule_count }}</span>
                            </button>
                        </span>
                    </div>
                @else
                    <div class="d-flex align-items-center justify-content-end" data-kt-customer-table-toolbar="base">
                        <span id="instant-button">
                            <button type="button" class="btn btn-dark btn-icon btn-sm mb-2" data-kt-menu-trigger="click"
                                data-kt-menu-placement="bottom-start">

                                <span class="svg-icon svg-icon-5 rotate-180 "><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect x="10" y="10" width="4" height="4" rx="2" fill="black" />
                                        <rect x="17" y="10" width="4" height="4" rx="2" fill="black" />
                                        <rect x="3" y="10" width="4" height="4" rx="2" fill="black" />
                                    </svg></span>
                            </button>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-200px py-4"
                                data-kt-menu="true">
                                <div class="menu-item px-3 mb-2">
                                    <button type="button" class="btn btn-success btn-block btn-scale  "
                                        data-bs-toggle="modal" data-bs-target="#completed">
                                        Completed Orders
                                    </button>
                                </div>
                                <div class="menu-item px-3">

                                    <button type="button" class="btn btn-danger btn-block btn-scale  mt-2"
                                        data-bs-toggle="modal" data-bs-target="#cancelled">
                                        Cancelled Orders
                                    </button>
                                </div>
                                <div class="menu-item px-3">
                                    <button type="button" class="btn btn-dark btn-scale  mt-2" onclick="viewScheduled();"
                                        style="white-space:nowrap">
                                        Scheduled Orders <span class="ms-1 badge badge-dark">{{ $schedule_count }}</span>
                                    </button>
                                </div>
                            </div>
                        </span>
                    </div>
                @endif
                <div class="col-lg-9 text-end" id="scheduled-button" style="display: none">
                    @if ((new \Jenssegers\Agent\Agent())->isDesktop())
                        <button type="button" class="btn btn-success btn-scale me-5" data-bs-toggle="modal"
                            data-bs-target="#scheduledcompleted">
                            Completed Orders
                        </button>
                        <button type="button" class="btn btn-danger btn-scale me-5" data-bs-toggle="modal"
                            data-bs-target="#scheduledcancelled">
                            Cancelled Orders
                        </button>
                        <span id="instantcount">
                            <button type="button" class="btn btn-dark btn-scale  me-5" style="white-space:nowrap"
                                onclick="viewInstant();">
                                Instant Orders<span class="ms-2 badge badge-dark">{{ $instant_count }}</span>
                            </button>
                        </span>
                    @else
                        <div class="d-flex align-items-center justify-content-end" data-kt-customer-table-toolbar="base">
                            <button type="button" class="btn btn-dark btn-icon btn-sm mb-2" data-kt-menu-trigger="click"
                                data-kt-menu-placement="bottom-start">
                                <span class="svg-icon svg-icon-5 rotate-180 "><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect x="10" y="10" width="4" height="4" rx="2"
                                            fill="black" />
                                        <rect x="17" y="10" width="4" height="4" rx="2"
                                            fill="black" />
                                        <rect x="3" y="10" width="4" height="4" rx="2"
                                            fill="black" />
                                    </svg>
                                </span>
                            </button>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-200px py-4"
                                data-kt-menu="true">
                                <div class="menu-item px-3 mb-2">

                                    <button type="button" class="btn btn-success btn-scale me-5" data-bs-toggle="modal"
                                        data-bs-target="#scheduledcompleted">
                                        Completed Orders
                                    </button>
                                </div>
                                <div class="menu-item px-3">

                                    <button type="button" class="btn btn-danger btn-scale me-5" data-bs-toggle="modal"
                                        data-bs-target="#scheduledcancelled">
                                        Cancelled Orders
                                    </button>
                                </div>
                                <div class="menu-item px-3">
                                    <span id="instantcount">
                                        <button type="button" class="btn btn-dark btn-scale mt-2 me-5"
                                            onclick="viewInstant();">
                                            Instant Orders<span class="badge badge-dark">{{ $instant_count }}</span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div id="searched-order" style="display: none">
            <div class="row" id="searchorders">
                <div class="px-5" id="loading-indicator" style="display: none">
                    Loading...
                </div>
                <div class="fs-2" id="nodata-indicator" style="display: none">
                    No Data Found
                </div>

            </div>
        </div>

        <div id="instant">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card shadow-sm" id="neworders">
                        <div class="d-flex align-items-center p-3">
                            <span class="ps-1 fw-bolder fs-4">New Orders</span>
                        </div>
                        <hr style="margin:0px" />
                        <div class="px-3">
                            @foreach ($newOrders as $key => $order)
                                <div class="d-flex flex-column py-2">
                                    <div class="d-flex align-items-center">
                                        <em class="icon ni ni-user-alt-fill"></em>
                                        <span class="pl-2 text-dark text-uppercase">
                                            {{ $order->user ? $order->user->name : '' }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center flex-row justify-content-between mt-2">
                                        <span class="text-dark text-uppercase"> {{ $order->unique_order_id }}</span>
                                        <span class=" badge bg-success  pull-right">
                                            {{ $order->orderstatus->name }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center flex-row justify-content-between mt-2">
                                        <span class="text-dark text-uppercase"> ₹ {{ $order->total }}</span>
                                        <span class="  pull-right">
                                            {{ $order->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center flex-row justify-content-between mt-2">
                                        @if ($order->is_schedule)
                                            <span class="badge bg-purple">Scheduled</span>
                                        @else
                                            <span class="badge bg-purple">Instant</span>
                                        @endif
                                        <span class="pull-right">
                                            <a href="{{ route('admin.orderView', $order->unique_order_id) }}"
                                                class="btn btn-primary">View</a>
                                        </span>
                                    </div>
                                </div>
                                <hr />
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm" id="acceptedOrders">
                        <div class="d-flex align-items-center p-3">
                            <span class="ps-1 fw-bolder fs-4">Accepted Orders</span>
                        </div>
                        <hr style="margin:0px" />
                        <div class="px-3 ">
                            @foreach ($acceptedOrders as $key => $order)
                                <div class="d-flex flex-column py-2">
                                    <div class="d-flex align-items-center flex-row">
                                        <em class="icon ni ni-user-alt-fill"></em>
                                        <span class="pl-2 text-dark text-uppercase">
                                            {{ $order->user ? $order->user->name : '' }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center flex-row justify-content-between mt-2">
                                        <span class="text-dark text-uppercase"> {{ $order->unique_order_id }}</span>
                                        <span class=" badge bg-success pull-right">
                                            {{ $order->orderstatus->name }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center flex-row justify-content-between mt-2">
                                        <span class="text-dark text-uppercase"> ₹ {{ $order->total }}</span>
                                        <span class="pull-right">
                                            {{ $order->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center flex-row justify-content-between mt-2">
                                        @if ($order->is_schedule)
                                            <span class="badge bg-purple">Scheduled</span>
                                        @else
                                            <span class="badge bg-purple">Instant</span>
                                        @endif
                                        <span class="pull-right">
                                            <a href="{{ route('admin.orderView', $order->unique_order_id) }}"
                                                class="btn btn-primary">View</a>
                                        </span>
                                    </div>
                                </div>
                                <hr />
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm" id="ongoingOrders">
                        <div class="d-flex align-items-center p-3">
                            <span class="ps-1 fw-bolder fs-4">Ongoing Orders</span>
                        </div>
                        <hr style="margin:0px" />
                        <div class="px-3 ">
                            @foreach ($ongoingOrders as $key => $order)
                                <div class="d-flex flex-column py-2">
                                    <div class="d-flex align-items-center">
                                        <em class="icon ni ni-user-alt-fill"></em>
                                        <span
                                            class="pl-2 text-dark text-uppercase">{{ $order->user ? $order->user->name : '' }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center flex-row justify-content-between mt-2">
                                        <span class="text-dark text-uppercase"> {{ $order->unique_order_id }}</span>
                                        <span class=" badge bg-success  pull-right">
                                            {{ $order->orderstatus->name }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center flex-row justify-content-between mt-2">
                                        <span class="text-dark text-uppercase"> ₹ {{ $order->total }}</span>
                                        <span class="  pull-right">
                                            {{ $order->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center flex-row justify-content-between mt-2">
                                        @if ($order->is_schedule)
                                            <span class="badge bg-purple">Scheduled</span>
                                        @else
                                            <span class="badge bg-purple">Instant</span>
                                        @endif
                                        <span class="pull-right">
                                            <a href="{{ route('admin.orderView', $order->unique_order_id) }}"
                                                class="btn btn-primary">View</a>
                                        </span>
                                    </div>
                                </div>
                                <hr />
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="scheduled" style="display: none">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card shadow-sm" id="newScheduledOrders">
                        <div class="d-flex align-items-center p-3">
                            <span class="ps-1 fw-bolder fs-4">New Orders</span>
                        </div>
                        <hr style="margin:0px" />
                        <div class="px-3 ">
                            @foreach ($newScheduledOrders as $key => $order)
                                <div class="d-flex flex-column py-2">
                                    <div class="d-flex align-items-center">
                                        <em class="icon ni ni-user-alt-fill"></em>
                                        <span
                                            class="pl-2 text-dark text-uppercase">{{ $order->user ? $order->user->name : '' }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center flex-row justify-content-between mt-2">
                                        <span class="text-dark text-uppercase"> {{ $order->unique_order_id }}</span>
                                        <span class=" badge bg-success  pull-right">
                                            {{ $order->orderstatus->name }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center flex-row justify-content-between mt-2">
                                        <span class="text-dark text-uppercase"> ₹ {{ $order->total }}</span>
                                        <span class="  pull-right">
                                            {{ $order->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center flex-row justify-content-between mt-2">
                                        @if ($order->is_schedule)
                                            <span class="badge bg-purple">Scheduled</span>
                                        @else
                                            <span class="badge bg-purple">Instant</span>
                                        @endif
                                        <span class="pull-right d-flex align-items-center">
                                            @if (isset($order->schedule_date) && $order->schedule_time)
                                                @php
                                                    $scheduleDate = Carbon\Carbon::createFromFormat(
                                                        'Y-m-d',
                                                        $order->schedule_date,
                                                    )->startOfDay();
                                                    $currentDate = Carbon\Carbon::now()->startOfDay();
                                                @endphp

                                                @if (isset($scheduleDate) && $scheduleDate->diffInDays($currentDate) == 0)
                                                    <span class="badge bg-success">TODAY</span>
                                                    <i class="text-dark fw-bold d-flex align-items-center">
                                                        <em class="icon ni ni-clock-fill fs-4"></em>
                                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $order->schedule_time)->format('h:i A') }}
                                                    </i>
                                                @else
                                                    <i class="text-dark fw-bold d-flex align-items-center">
                                                        <em class="icon ni ni-calender-date-fill fs-4"></em>
                                                        <span class="fw-bold ms-2">{{ $order->schedule_date }} </span>
                                                    </i>
                                                @endif
                                            @else
                                                <span>Invalid date or time</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="text-end mt-2">
                                        <a href="{{ route('admin.orderView', $order->unique_order_id) }}"
                                            class="btn btn-primary">View</a>
                                    </div>
                                </div>
                                <hr />
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm" id="acceptedScheduledOrders">
                        <div class="d-flex align-items-center p-3">
                            <span class="ps-1 fw-bolder fs-4">Accepted Orders</span>
                        </div>
                        <hr style="margin:0px" />
                        <div class="px-3 ">
                            @foreach ($acceptedScheduledOrders as $key => $order)
                                <div class="d-flex flex-column py-2">
                                    <div class="d-flex align-items-center">
                                        <em class="icon ni ni-user-alt-fill"></em>
                                        <span
                                            class="pl-2 text-dark text-uppercase">{{ $order->user ? $order->user->name : '' }}
                                        </span>
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center flex-row justify-content-between mt-2">
                                        <span class="text-dark text-uppercase"> {{ $order->unique_order_id }}</span>
                                        <span class=" badge bg-success  pull-right">
                                            {{ $order->orderstatus->name }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center flex-row justify-content-between mt-2">
                                        <span class="text-dark text-uppercase"> ₹ {{ $order->total }}</span>
                                        <span class="  pull-right">
                                            {{ $order->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center flex-row justify-content-between mt-2">
                                        @if ($order->is_schedule)
                                            <span class="badge bg-purple">Scheduled</span>
                                        @else
                                            <span class="badge bg-purple">Instant</span>
                                        @endif
                                        <span class="pull-right d-flex align-items-center">
                                            @if ($order->schedule_date && $order->schedule_time)
                                                @php
                                                    $scheduleDate = Carbon\Carbon::createFromFormat(
                                                        'Y-m-d',
                                                        $order->schedule_date,
                                                    )->startOfDay();
                                                    $currentDate = Carbon\Carbon::now()->startOfDay();
                                                @endphp

                                                @if ($scheduleDate->diffInDays($currentDate) == 0)
                                                    <span class="badge bg-success">TODAY</span>
                                                    <i class="text-dark fw-bold d-flex align-items-center">
                                                        <em class="icon ni ni-clock-fill fs-4"></em>
                                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $order->schedule_time)->format('h:i A') }}
                                                    </i>
                                                @else
                                                    <i class="text-dark fw-bold">
                                                        <em class="icon ni ni-calender-date-fill fs-4"></em>
                                                        <span class="fw-bold ms-2">{{ $order->schedule_date }} </span>
                                                    </i>
                                                @endif
                                            @else
                                                <span>Invalid date or time</span>
                                            @endif

                                        </span>
                                    </div>
                                    <div class="text-end mt-2">
                                        <a href="{{ route('admin.orderView', $order->unique_order_id) }}"
                                            class="btn btn-primary">View</a>
                                    </div>
                                </div>
                                <hr />
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm" id="ongoingScheduledOrders">
                        <div class="d-flex align-items-center p-3">
                            <span class="ps-1 fw-bolder fs-4">Ongoing Orders</span>
                        </div>
                        <hr style="margin:0px" />
                        <div class="px-3 ">
                            @foreach ($ongoingScheduledOrders as $key => $order)
                                <div class="d-flex flex-column py-2">
                                    <div class="d-flex align-items-center">
                                        <em class="icon ni ni-user-alt-fill"></em>
                                        <span
                                            class="pl-2 text-dark text-uppercase">{{ $order->user ? $order->user->name : '' }}
                                        </span>
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center flex-row justify-content-between mt-2">
                                        <span class="text-dark text-uppercase"> {{ $order->unique_order_id }}</span>
                                        <span class=" badge bg-success  pull-right">
                                            {{ $order->orderstatus->name }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center flex-row justify-content-between mt-2">
                                        <span class="text-dark text-uppercase"> ₹ {{ $order->total }}</span>
                                        <span class="  pull-right">
                                            {{ $order->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center flex-row justify-content-between mt-2">
                                        @if ($order->is_schedule)
                                            <span class="badge bg-purple">Scheduled</span>
                                        @else
                                            <span class="badge bg-purple">Instant</span>
                                        @endif
                                        <span class="pull-right d-flex align-items-center">
                                            @if (isset($order->schedule_date) && $order->schedule_time)
                                                @php
                                                    $scheduleDate = Carbon\Carbon::createFromFormat(
                                                        'Y-m-d',
                                                        $order->schedule_date,
                                                    )->startOfDay();
                                                    $currentDate = Carbon\Carbon::now()->startOfDay();
                                                @endphp

                                                @if (isset($scheduleDate) && $scheduleDate->diffInDays($currentDate) == 0)
                                                    <span class="badge bg-success">TODAY</span>
                                                    <i class="text-dark fw-bold d-flex align-items-center">
                                                        <em class="icon ni ni-clock-fill fs-4"></em>

                                                    </i>
                                                @else
                                                    <i class="text-dark fw-bold d-flex align-items-center">
                                                        <em class="icon ni ni-calender-date-fill fs-4"></em>
                                                        <span class="fw-bold ms-2">{{ $order->schedule_date }} </span>
                                                    </i>
                                                @endif
                                            @else
                                                <span>Invalid date or time</span>
                                            @endif
                                        </span>
                                    </div>

                                    <div class="text-end mt-2">
                                        <a href="{{ route('admin.orderView', $order->unique_order_id) }}"
                                            class="btn btn-primary">View</a>
                                    </div>
                                </div>
                                <hr />
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="completed">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Completed Instant Orders</h5>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="completedInstant" class="table align-middle table-row-dashed fs-6 gy-5 text-center">
                            <thead>
                                <tr class="text-center fw-bolder fs-7 text-uppercase gs-0">
                                    <th>Unique Order Id</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold text-center">
                                @foreach ($completedOrders as $order)
                                    <tr>
                                        <td>{{ $order->unique_order_id }}</td>
                                        <td>{{ $order->user ? $order->user->name : '' }}
                                            </span></td>
                                        <td>{{ $order->total }}</td>
                                        <td>{{ $order->orderstatus->name }}</td>
                                        <td>{{ $order->created_at->diffForHumans() }}</td>
                                        <td><a href="{{ route('admin.orderView', $order->unique_order_id) }}"
                                                class="btn btn-primary"><i class="bi bi-eye"></i></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="cancelled">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancelled Instant Orders</h5>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="cancelledInstant" class="table align-middle table-row-dashed fs-6 gy-5 text-center">
                            <thead>
                                <tr class="text-center fw-bolder fs-7 text-uppercase gs-0">
                                    <th>Unique Order Id</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold text-center">
                                @foreach ($cancelledOrders as $order)
                                    <tr>
                                        <td>{{ $order->unique_order_id }}</td>
                                        <td>{{ $order->user ? $order->user->name : '' }}
                                            </span></td>
                                        <td>{{ $order->total }}</td>
                                        <td>{{ $order->orderstatus->name }}</td>
                                        <td>{{ $order->created_at->diffForHumans() }}</td>
                                        <td><a href="{{ route('admin.orderView', $order->unique_order_id) }}"
                                                class="btn btn-primary"><i class="bi bi-eye"></i></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="scheduledcompleted">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Completed Scheduled Orders</h5>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="completedSchedule" class="table align-middle table-row-dashed fs-6 gy-5 text-center">
                            <thead>
                                <tr class="text-center fw-bolder fs-7 text-uppercase gs-0">
                                    <th>Unique Order Id</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold text-center">
                                @foreach ($completedScheduledOrders as $order)
                                    <tr>
                                        <td>{{ $order->unique_order_id }}</td>
                                        <td>{{ $order->user ? $order->user->name : '' }}
                                            </span></td>
                                        <td>{{ $order->total }}</td>
                                        <td>{{ $order->orderstatus->name }}</td>
                                        <td>{{ $order->created_at->diffForHumans() }}</td>
                                        <td><a href="{{ route('admin.orderView', $order->unique_order_id) }}"
                                                class="btn btn-primary"><i class="bi bi-eye"></i></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="scheduledcancelled">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancelled Scheduled Orders</h5>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="cancelledScheduled" class="table align-middle table-row-dashed fs-6 gy-5 text-center">
                            <thead>
                                <tr class="text-center fw-bolder fs-7 text-uppercase gs-0">
                                    <th>Unique Order Id</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold text-center">
                                @foreach ($cancelledScheduledOrders as $order)
                                    <tr>
                                        <td>{{ $order->unique_order_id }}</td>
                                        <td>{{ $order->user ? $order->user->name : '' }}
                                            </span></td>
                                        <td>{{ $order->total }}</td>
                                        <td>{{ $order->orderstatus->name }}</td>
                                        <td>{{ $order->created_at->diffForHumans() }}</td>
                                        <td><a href="{{ route('admin.orderView', $order->unique_order_id) }}"
                                                class="btn btn-primary"><i class="bi bi-eye"></i></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function changeCity() {
            var city_id = $('#city_id').val();
            let url = "{{ route('admin.viewLiveOrders', '') }}/" + city_id;
            // alert(url);
            url = url.replace(':city_id', city_id);
            document.location.href = url;
        }
    </script>
    <script>
        function viewScheduled() {
            $('#all-buttons').show();
            $('#instanthead').hide();
            $('#scheduledhead').show();
            $('#instant').hide();
            $('#scheduled').show();
            $('#instant-button').hide();
            $('#scheduled-button').show();
            $('#select-city').show();
        }

        function viewInstant() {
            $('#all-buttons').show();
            $('#instanthead').show();
            $('#scheduledhead').hide();
            $('#instant').show();
            $('#scheduled').hide();
            $('#instant-button').show();
            $('#scheduled-button').hide();
            $('#select-city').show();

        }
    </script>
    <script>
        function closeSearch() {
            $('#all-buttons').show();
            $('#select-city').show();
            $('#instanthead').show();
            $('#scheduledhead').hide();
            $('#searchedhead').hide();
            $('#searched-order').hide();
            $('#instant').show();
            $('#scheduled').hide();
            $('#close-button').hide();
            $('#search_order_id').val('');
        }

        function searchOrderId() {
            var searchOrderValue = $('#search_order_id').val();
            if (searchOrderValue.length > 0) {
                fetchSearchOrders()
                $('#all-buttons').hide();
                $('#select-city').hide();
                $('#searchedhead').show();
                $('#searched-order').show();
                $('#instant').hide();
                $('#scheduled').hide();
                $('#close-button').show();
            } else {
                $('#all-buttons').show();
                $('#select-city').show();
                $('#instanthead').show();
                $('#scheduledhead').hide();
                $('#searchedhead').hide();
                $('#searched-order').hide();
                $('#instant').show();
                $('#scheduled').hide();
                $('#close-button').hide();
            }
        }

        function fetchSearchOrders() {
            $('#loading-indicator').show();
            $.ajax({
                type: "get",
                url: '{{ route('admin.ajaxLiveSearchOrders') }}',
                data: {
                    'search': $('#search_order_id').val()
                },
                success: function(data) {
                    $('.search-order-remove').remove();
                    $('#loading-indicator').hide();
                    if (data.length > 0) {
                        $('#nodata-indicator').hide();
                        $.each(data, function(index, value) {
                            var wildCard = '<div class="search-order-remove col-lg-4">' +
                                '<div class="card shadow-sm">' +
                                '<div class="card-body">' +
                                '<div class="d-flex flex-column py-2">' +
                                '<div class="d-flex align-items-center flex-row justify-content-between">' +
                                '<em class="icon ni ni-user-alt-fill"></em>' +
                                '<span class="pl-2 text-dark text-uppercase">' + (value.user ? value
                                    .user
                                    .name : '') + '</span>' +
                                '</div>' +
                                '<div class="d-flex align-items-center flex-row justify-content-between mt-2">' +
                                '<span class="text-dark text-uppercase">' + value.unique_order_id +
                                '</span>' +
                                '<span class=" badge bg-success pull-right">' + value.orderstatus
                                .name +
                                '</span>' +
                                '</div>' +
                                '<div class="d-flex align-items-center flex-row justify-content-between mt-2">' +
                                '<span class="text-dark text-uppercase"> ₹ ' + value.total +
                                '</span>' +
                                '<span class="pull-right">' + (value.store ? value.store.name : '') +
                                '</span>' +
                                '</div>' +
                                '<div class="d-flex align-items-center flex-row justify-content-between mt-2">' +
                                (value.is_schedule ?
                                    '<span class="badge bg-purple">Scheduled</span>' :
                                    '<span class="badge bg-purple">Instant</span>') +
                                '<span class="pull-right">' + value.formatted_created_at + '</span>' +
                                '</div>' +
                                '<div class="d-flex align-items-center flex-row justify-content-between mt-2">' +
                                (value.is_medicine === 1 ?
                                    '<span class="badge badge-danger">Medicine</span>' :
                                    (value.is_any_store === 1 ?
                                        '<span class="badge badge-info">Any Store</span>' : '')) +
                                '<div class="pull-right">' +
                                '<a href="{{ route('admin.orderView', '') }}/' + value
                                .unique_order_id +
                                '"' +
                                'class="btn btn-primary btn-sm">View</a>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>';
                            console.log(value.unique_order_id, 'data');
                            $('#searchorders').append(wildCard);
                        });
                    } else {
                        $('#nodata-indicator').show();
                    }
                }
            });
        }

        function fetchOrders() {
            $.ajax({
                type: "get",
                url: '{{ url('/admin/ajax-live-orders') }}',
                success: function(data) {
                    $('#neworders').load(document.URL + ' #neworders');
                    $('#acceptedOrders').load(document.URL + ' #acceptedOrders');
                    $('#ongoingOrders').load(document.URL + ' #ongoingOrders');
                    $('#newScheduledOrders').load(document.URL + ' #newScheduledOrders');
                    $('#acceptedScheduledOrders').load(document.URL + ' #acceptedScheduledOrders');
                    $('#ongoingScheduledOrders').load(document.URL + ' #ongoingScheduledOrders');
                    $('#completedInstant').load(document.URL + ' #completedInstant');
                    $('#cancelledInstant').load(document.URL + ' #cancelledInstant');
                    $('#cancelledScheduled').load(document.URL + ' #cancelledScheduled');
                    $('#completedSchedule').load(document.URL + ' #completedSchedule');
                    $('#instantcount').load(document.URL + ' #instantcount');
                    $('#schedulecount').load(document.URL + ' #schedulecount');
                }
            });
        }
        $(document).ready(function() {
            var search_orderId = $('#search_order_id').val();
            if (search_orderId) {
                setInterval(fetchSearchOrders, 3000);
            } else {
                setInterval(fetchOrders, 3000);
            }
        });
    </script>
@endsection
