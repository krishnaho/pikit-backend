@extends('admin.layout.master')
@section('title')
    Order View
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">View Order</h4>
                </div>
            </div>
        </div>
        <div class="row ">
            <div class="col-lg-4">
                <div class="card">
                    <div class="d-flex align-items-center p-3">
                        <em class="icon ni ni-building-fill fs-4"></em> <span class="ps-1 fw-bolder fs-4">Restaurant</span>
                    </div>
                    <hr style="margin:0px" />
                    <div class="card-body">
                        @if ($order->restaurant)
                            <a href="{{ route('admin.editRestaurant', $order->restaurant->id) }}" style="color:#526484">
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center ">
                                        <em class="icon ni ni-alert-circle-fill fs-6 pe-1"></em>
                                        {{ $order->restaurant->name }}
                                    </div>
                                    <div class="d-flex align-items-center mt-2 ">
                                        <em class="icon ni ni-call fs-6 pe-1"></em>{{ $order->restaurant->phone }}
                                    </div>
                                    <div class="d-flex align-items-center mt-2 ">
                                        <em class="icon ni ni-map-pin fs-6 pe-1"></em>{{ $order->restaurant->address }}
                                    </div>
                                    <div class="d-flex align-items-center mt-2 ">
                                        <em class="icon ni ni-location  fs-6 pe-1"></em>{{ $order->restaurant->land_mark }}
                                    </div>
                                </div>
                            </a>
                        @else
                            <a>
                                <div
                                    class="d-flex flex-column justify-content-center align-content-center align-items-center">
                                    <div class="text-center mt-2">
                                        <span class="fw-bolder ">
                                            {{ $order->restaurant_name }}
                                        </span>
                                    </div>
                                    <div class="mt-2 ">
                                        @if ($order->restaurant_address)
                                            {{ json_decode($order->restaurant_address)->address }}
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            {{-- user data --}}
            <div class="col-lg-4">
                <div class="card ">
                    <div class="d-flex align-items-center p-3">
                        <em class="icon ni ni-user-list fs-4"></em> <span class="ps-1 fw-bolder fs-4">Customer</span>
                    </div>
                    <hr style="margin:0px" />
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            @if ($order->user)
                                <a href="{{ route('admin.editUser', $order->user->id) }}" style="color: #526484">
                                    <div class="">
                                        <em class="icon ni ni-user-alt-fill"></em>
                                        <span class="pl-2">{{ $order->user->name }}
                                        </span>
                                    </div>
                                </a>
                            @endif
                            @if ($order->user)
                                <div class="mt-2">
                                    <em class="icon ni ni-call fs-6 pe-1"></em>
                                    <span class="pl-2">{{ $order->user->phone ?? '--' }}</span>

                                </div>
                            @endif
                            @if ($order->user)
                                <div class="mt-2">
                                    <em class="icon ni ni-mail-fill fs-6 pe-1"></em>
                                    <span class="pl-2">{{ $order->user->email }}</span>

                                </div>
                            @endif
                            @if ($order->payment_mode)
                                <div class="mt-2">
                                    <em class="icon ni ni-cc-alt-fill fs-6 pe-1"></em>
                                    <span class="pl-2">{{ $order->payment_mode }}</span>
                                </div>
                            @endif
                            @if ($order->address)
                                <div class="mt-2">
                                    <div class="d-flex align-items-center fs-6 pe-1"></em>
                                        <span class="pl-2">{{ $order->address }}</span>
                                    </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {{-- fleet data --}}
            <div class="col-lg-4">
                <div class="card ">
                    <div class="d-flex align-items-center p-3">
                        <em class="icon ni ni-opt fs-4"></em> <span class="ps-1 fw-bolder fs-4">Fleet Management
                            Order</span>
                    </div>
                    <hr style="margin:0px" />
                    <div class="card-body">
                        @if ($order->agent_phone && $order->agent_name)
                            <div class="d-flex flex-column py-3">
                                <div>
                                    <div class="d-flex flex-row justify-content-around">
                                        <div>
                                            @if ($order->agent_image)
                                                <img class="img-fluid"
                                                    style="height:5vw; width:5vw; object-fit:cover; border-radius:10px"
                                                    src="https://fleet.howincloud.com/{{ $order->agent_image }}"
                                                    alt="grosav">
                                            @else
                                                <img class="img-fluid"
                                                    style="height:5vw; width:5vw; object-fit:cover; border-radius:100%"
                                                    src="{{ asset('assets/images/user.jpg') }}" alt="grosav">
                                            @endif
                                        </div>
                                        <div class="d-flex flex-column justify-content-start fw-bolder">
                                            <div class="d-flex align-items-center">
                                                <em
                                                    class="icon ni ni-user-alt-fill fs-6 pe-1"></em>{{ $order->agent_name }}
                                            </div>
                                            <div class="d-flex align-items-center mt-2 ">
                                                <em class="icon ni ni-call fs-6 pe-1"></em>{{ $order->agent_phone }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            @if ($order->orderstatus->id != 6)
                                <div class="d-flex flex-column align-items-center justify-content-center py-5">
                                    <span class="fw-bolder">Waiting For Fleet Details.....</span>
                                </div>
                            @else
                                <div class="d-flex flex-column align-items-center justify-content-center py-5">
                                    <span class="badge bg-danger">Order Cancelled</span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-lg-8">
                <div class="card">
                    <div class="d-flex align-items-center justify-content-between p-3">
                        <div>
                            <em class="icon ni ni-package fs-4"></em> <span class="ps-1 fw-bolder fs-4">Order Details</span>
                        </div>
                        <span class="mt-7">Order ID:
                            <span class="badge bg-success"><b>{{ $order->unique_order_id }}</b> </span>
                        </span>
                    </div>
                    <hr style="margin:0px" />
                    @php
                        function calculateAddonTotal($addons)
                        {
                            $total = 0;
                            foreach ($addons as $addon) {
                                $total += $addon->price * $addon->quantity;
                            }
                            return $total;
                        }
                    @endphp
                    @php
                        $sub = 0;
                        foreach ($order->orderitems as $item) {
                            $sub += $item->price * $item->quantity + calculateAddonTotal($item->orderitemaddons);
                        }
                    @endphp
                    <div class="card-body d-flex flex-column  pt-4">
                        @foreach ($order->orderitems as $key => $item)
                            @if ($item)
                                <div class="">
                                    <div class="d-flex flex-row justify-content-between flex-nowrap mt-2">
                                        <div class=" flex-grow-1 w-50px">
                                            <span class="fw-bolder"> {{ $key + 1 }}. {{ $item->name }}</span>
                                        </div>
                                        <div class="flex-grow-1 w-50px ">
                                            <span>
                                                @if ($item->price > 0)
                                                    ₹{{ $item->price }} x {{ $item->quantity }}
                                                @else
                                                    --
                                                @endif
                                            </span>
                                        </div>
                                        <div class=" flex-shrink-1 text-right">
                                            @if ($item->price > 0)
                                                ₹ {{ number_format($item->price * $item->quantity, 2) }}
                                            @else
                                                --
                                            @endif
                                            <span>
                                            </span>
                                            @if ($order->order_status_id == 1)
                                                <span class="mt-6 delete_order" data-bs-toggle="modal"
                                                    data-bs-target="#delete_order_item" id="add"
                                                    data-id={{ $item->id }} data-name={{ $item->name }}
                                                    data-quantity={{ $item->quantity }}><i
                                                        class="bi bi-trash text-dark"></i></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <hr class=" mt-2 mb-2">
                                @php
                                    $item_removals = json_decode($item->removals);
                                    // dd($item_removals);
                                @endphp
                                @if (isset($item->removals) && count($item_removals) > 0)
                                    <div class="d-flex flex-row justify-content-end align-items-center">
                                        <span class="fw-bold fs-5 text-danger">Remove these from item:
                                            @foreach ($item_removals as $removal)
                                                <span class="badge bg-danger">
                                                    {{ $removal->removal_name }}
                                                </span>
                                            @endforeach
                                    </div>
                                @endif
                                @if (calculateAddonTotal($item->orderitemaddons) > 0)
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card-body p-0">
                                                <div class="table-responsive d-flex justify-content-end">
                                                    <table
                                                        class="table table-striped table-bordered text-nowrap w-50 text-center">
                                                        @foreach ($item->orderitemaddons as $key => $addon)
                                                            @if ($key == 0)
                                                                <thead>
                                                                    <tr class="p-0 ">
                                                                        <th class="p-2 fw-bolder text-start">ADDON</th>
                                                                        <th class="p-2 fw-bolder text-start">QUANTITY</th>
                                                                        <th class="p-2 fw-bolder text-start">PRICE</th>
                                                                    </tr>
                                                                </thead>
                                                            @endif
                                                        @endforeach
                                                        <tbody>
                                                            @foreach ($item->orderitemaddons as $key => $addon)
                                                                <tr>
                                                                    <td class="text-start px-2">{{ $addon->name }}</td>
                                                                    <td class="text-start px-2">{{ $addon->quantity }}
                                                                    </td>
                                                                    <td class="text-start px-2">
                                                                        ₹ {{ number_format($addon->price, 2) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end mt-2">
                                        <span class="pe-2">Addon Total:</span> <span>₹
                                            {{ number_format($item->price * $item->quantity + calculateAddonTotal($item->orderitemaddons), 2) }}
                                        </span>
                                    </div>
                                    <hr class=" mt-2 mb-2">
                                @endif
                            @endif
                        @endforeach
                        <div class="d-flex flex-column align-content-center justify-content-center align-items-end">
                            <div class="">
                                <div class="d-flex flex-row justify-content-between align-items-center">
                                    <div class="flex-grow-1  pe-2">
                                        <label class="col-form-label p-0 fs-8">Item Total :</label>
                                    </div>
                                    <div class="text-right flex-grow-1 fs-8 w-75px">
                                        ₹ {{ number_format($sub, 2) }}
                                    </div>
                                </div>
                                @if ($order->coupon_code)
                                    <div class="">
                                        <div class="d-flex flex-row justify-content-between align-items-center">
                                            <div class="flex-grow-1 pe-2">
                                                <label class="col-form-label p-0 fs-8">Coupon:</label>
                                            </div>
                                            <div class="text-right flex-grow-1 fs-8 w-75px">
                                                {{ $order->coupon_code }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="">
                                    <div class="d-flex flex-row justify-content-between align-items-center">
                                        <div class="text-right   flex-grow-1 pe-2">
                                            <label class="col-form-label fw-bolder">Sub Total :</label>
                                        </div>
                                        <div class="text-right flex-grow-1 fw-bolder w-75px">
                                            ₹ {{ $order->sub_total }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr style="margin:0px" />
                    <div class="card-body pt-3 text-right">
                        <div>
                            <div class="d-flex flex-column align-content-center justify-content-center align-items-end">
                                <div>
                                    @if ($order->surge_fee)
                                        <div class="d-flex flex-row align-items-center justify-content-between">
                                            <div class="flex-grow-1 text-right">
                                                <label for="" class="col-form-label p-0 fs-8 pe-2">Surge
                                                    Fee:</label>
                                            </div>
                                            <div class=" w-75px text-right fs-8">
                                                ₹ {{ number_format($order->surge_fee, 2) }}
                                            </div>
                                        </div>
                                    @endif
                                    <div class="d-flex flex-row align-items-center justify-content-between">
                                        <div class="flex-grow-1 text-right">
                                            <label for="" class="col-form-label p-0 fs-8 pe-2 ">Delivery
                                                Charge:</label>
                                        </div>
                                        <div class="flex-grow-1 w-75px text-right fs-8">
                                            ₹ {{ number_format($order->delivery_charge, 2) }}
                                        </div>
                                    </div>
                                    <div class="d-flex flex-row align-items-center justify-content-between">
                                        <div class="flex-grow-1 text-right">
                                            <label for="" class="col-form-label p-0 fs-8 pe-2">Tax:</label>
                                        </div>
                                        <div class=" w-75px text-right fs-8">
                                            ₹ {{ number_format($order->tax, 2) }}
                                        </div>
                                    </div>
                                    <div class="d-flex flex-row align-items-center justify-content-between">
                                        <div class="flex-grow-1 text-right">
                                            <label for="" class="col-form-label p-0 fs-8 pe-2">Store
                                                Charge:</label>
                                        </div>
                                        <div class=" w-75px text-right fs-8">
                                            ₹ {{ $order->restaurant ? number_format($order->restaurant_charges, 2) : '' }}
                                        </div>
                                    </div>
                                    @if ($order->coupon_discount > 0)
                                        <div class="d-flex flex-row align-items-center justify-content-between">
                                            <div class="flex-grow-1 text-right">
                                                <label for="" class="col-form-label p-0 fs-8 pe-2">Coupon
                                                    Discount:</label>
                                            </div>
                                            <div class=" w-75px text-right fs-8">
                                                - ₹ {{ $order->coupon_discount ? $order->coupon_discount : '' }}
                                            </div>
                                        </div>
                                    @endif

                                    <div class="">
                                        <div
                                            class="d-flex flex-row align-items-center justify-content-between fw-bolder mt-2">
                                            <div class="flex-grow-1 text-right">
                                                <label for="" class="fs-4 pe-2">Total:</label>
                                            </div>
                                            <div class="w-75px text-right fs-4">
                                                <label class="">
                                                    ₹ {{ $order->total }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="d-flex align-items-center justify-content-between p-3">
                        <div>
                            <em class="icon ni ni-layers-fill fs-4"></em> <span class="ps-1 fw-bolder fs-4">Order
                                Status</span>
                        </div>
                        @if ($order->orderstatus->name == 'Order Placed')
                            <div class="text-center mt-7">
                                <span class="badge bg-purple">Order Placed</span>
                            </div>
                        @elseif($order->orderstatus->name == 'Order Accepted')
                            <div class="text-center mt-7">
                                <span class="badge bg-success">Order Accepted</span>
                            </div>
                        @elseif($order->orderstatus->name == 'Order Preparing')
                            <div class="text-center mt-7">
                                <span class="badge bg-info">Order Preparing</span>
                            </div>
                        @elseif($order->orderstatus->name == 'Ready to Pick Up')
                            <div class="text-center mt-7">
                                <span class="badge bg-warning">Ready to Pick Up</span>
                            </div>
                        @elseif($order->orderstatus->name == 'Delivery Agent Assigned')
                            <div class="text-center mt-7">
                                <span class="badge bg-warning">Delivery Agent Assigned</span>
                            </div>
                        @elseif($order->orderstatus->name == 'Picked Up')
                            <div class="text-center mt-7">
                                <span class="badge bg-warning">Picked Up</span>
                            </div>
                        @elseif($order->orderstatus->name == 'Completed')
                            <div class="text-center mt-7">
                                <span class="badge bg-success">Completed</span>
                            </div>
                        @elseif($order->orderstatus->name == 'Cancelled')
                            <div class="text-center mt-7">
                                <span class="badge bg-danger">Cancelled</span>
                            </div>
                        @elseif($order->orderstatus->name == 'Transaction Failed')
                            <div class="text-center mt-7">
                                <span class="badge bg-danger">Transaction Failed</span>
                            </div>
                        @elseif($order->orderstatus->name == 'Transaction Pending')
                            <div class="text-center mt-7">
                                <span class="badge bg-light">Transaction Pending</span>
                            </div>
                        @elseif($order->orderstatus->name == 'Self PickUp')
                            <div class="text-center mt-7">
                                <span class="badge bg-success">Self PickUp</span>
                            </div>
                        @endif
                    </div>
                    <hr style="margin:0px" />
                    <div class="card-body  d-flex flex-column  ">
                        @if ($order->is_schedule == 1)
                            <div>
                                <div class="d-flex flex-center">
                                    <span class="badge bg-light-info">Scheduled</span>
                                </div>
                            </div>
                            <div class="border-bottom-dashed border-dark border-1">
                                <div class="d-flex flex-row justify-content-between mb-3">
                                    <div class="btn btn-dark text-white btn-round ">
                                        <em class="icon ni ni-clock-fill"></em>
                                        <span class="fw-bold ms-2">{{ $order->schedule_time }}</span>
                                    </div>
                                    <div class="btn btn-dark text-white btn-round ">
                                        <em class="icon ni ni-calender-date-fill"></em>
                                        <span class="fw-bold ms-2">{{ $order->schedule_date }} </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="mt-3 border-bottom-dashed border-dark border-1 pb-3">
                            <div class=" d-flex flex-row justify-content-between align-items-center">
                                @if ($order->order_status_id == 1)
                                    <div class="">
                                        <form action="{{ route('admin.acceptOrderByAdmin') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $order->id }}">
                                            <button type="submit" class="btn   btn-success">Accept
                                                Order</button>
                                        </form>
                                    </div>
                                @endif
                                @if ($order->order_status_id == 1 || $order->order_status_id == 2)
                                    <div class="">
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#cancelOrder"
                                            class="  btn  btn-danger ">
                                            Cancel Order
                                        </button>
                                    </div>
                                    <div class="modal fade" tabindex="-1" id="cancelOrder">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"> Cancel Order</h5>
                                                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                                                        data-bs-dismiss="modal" aria-label="Close">
                                                        <span class="svg-icon svg-icon-2x"
                                                            style="font-size: 16px">&times;</span>
                                                    </div>
                                                </div>
                                                <div class="modal-body">
                                                    <p>
                                                        Do You Really want to Cancel this Order ?
                                                    </p>
                                                </div>
                                                <div class="modal-footer flex-right">
                                                    <button type="reset" data-bs-dismiss="modal"
                                                        class="btn btn-light me-3"> Close</button>
                                                    <a href="{{ route('admin.rejectOrderFromAdmin', $order->id) }}"
                                                        type="button" class="btn btn-danger">
                                                        Yes
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if ($order->order_status_id == 5)
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="badge bg-success">This Order Has Completed.</span>
                                </div>
                            @endif
                            @if ($order->order_status_id == 6)
                                <div class=" d-flex align-items-center justify-content-center">
                                    <span class="badge bg-danger">This Order Has Canceled.</span>
                                </div>
                            @endif
                        </div>
                        @if ($logs->count() > 0)
                            <div class="d-flex align-items-center mt-4 mb-4">
                                <div class="timeline">
                                    @foreach ($logs as $log)
                                        @php
                                            if ($log->properties == '[]') {
                                                $log_orderstatus = 0;
                                            } else {
                                                $log_orderstatus = json_decode($log->properties)->orderstatus;
                                            }
                                        @endphp
                                        @if ($log_orderstatus != 0)
                                            <div class="timeline-item">
                                                <div class="timeline-status ">
                                                    <em class="icon ni ni-circle-fill text-purple"></em>
                                                </div>
                                                <div class="timeline-date fw-bolder  text-gray-800 fs-8">
                                                    {{ $log->created_at->format('h:i A') }}</div>
                                                <div class="timeline-data fw-bold text-gray-800 ">
                                                    {{ $log->description }}
                                                    @if ($log->causer_id)
                                                        <a href="{{ route('admin.editUser', $log->causer_id) }}"
                                                            class="text-dark">|@if ($log->causer)
                                                                {{ $log->causer->name }}
                                                            @else
                                                                ---
                                                            @endif
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal fade" id="delete_order_item" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered ">
                    <div class="modal-content">
                        <div class="modal-header">
                            @if (isset($item))
                                <h5 class="modal-title">Deleted Order Item<span
                                        class="badge bg-warning  itemname">{{ $item->name }}</span></h5>
                            @endif
                            <!--begin::Close-->
                            <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                                aria-label="Close">
                                <span class="svg-icon svg-icon-2x" style="font-size: 16px">&times;</span>
                            </div>
                            <!--end::Close-->
                        </div>
                        <div class="modal-body py-10 px-lg-17 ">
                            <p>
                                Do you really want to remove this item from you order
                            </p>
                            <div class="row">
                                <label class="col-lg-3 form-label">Quantity</label>
                                <div class="col-lg-3 pb-0">
                                    <div id="quantity">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer flex-right">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <a href="" type="button" class="btn btn-danger">
                                Remove
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Item Modal -->
            <div class="modal fade" tabindex="-1" id="add_item">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Items</h5>
                            <!--begin::Close-->
                            <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                                aria-label="Close">
                                <span class="svg-icon svg-icon-2x"></span>
                            </div>
                            <!--end::Close-->
                        </div>
                        <form action="{{ route('admin.addItems') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" value="{{ $order->id }}" name="id">
                            <div class="modal-body">
                                <div class='form-group row mb-4'>
                                    <label class=" col-lg-3 required form-label">Name</label>
                                    <div class="col-lg-9">
                                        <select name="item" class="form-select" id="item"
                                            data-dropdown-parent='#add_item' data-control='select2'>
                                            @if ($order->restaurant && $order->restaurant->items)
                                                @foreach ($order->restaurant->items as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class='form-group row mb-4 addonCategory'>
                                </div>
                                <div class='form-group row mb-4 addons'>
                                </div>
                                <div id="cart-addon" style="color:black;display:none">
                                    No Quantity
                                </div>
                                <div class='form-group row mb-4'>
                                    <label class=" col-lg-3 required form-label">Quantity</label>
                                    <!--begin::Dialer-->
                                    <div class="position-relative w-md-150px text-center" data-kt-dialer="true"
                                        data-kt-dialer-min="1" data-kt-dialer-max="{{ $item->max_quantity }}"
                                        data-kt-dialer-step="1">

                                        <!--begin::Decrease control-->
                                        <button type="button"
                                            class="btn btn-icon btn-sm btn-primary position-absolute translate-middle-y"
                                            style="top:20px;left:16px" data-kt-dialer-control="decrease">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <!--end::Decrease control-->

                                        <!--begin::Input control-->
                                        <input type="number" class="form-control  form-control-solid border-1 ps-20"
                                            data-kt-dialer-control="input" placeholder="{{ $item->max_quantity }}"
                                            name="quantity" readonly value="0" />
                                        <!--end::Input control-->

                                        <!--begin::Increase control-->
                                        <button type="button" style="top:20px;right:16px"
                                            class="btn btn-icon btn-sm btn-primary position-absolute translate-middle-y"
                                            data-kt-dialer-control="increase">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                        <!--end::Increase control-->
                                    </div>
                                    <!--end::Dialer-->
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" id="prevent">Update</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                $('.delete_order').click(function() {
                    var id = $(this).data('id')
                    var name = $(this).data('name')
                    var quantity = $(this).data('quantity')
                    console.log(name)
                    console.log(quantity)
                    $('.itemname').text(name)
                    var a = $('#delete_order_item a')
                    a.attr('href', `/public/admin/delete-item/${id}/${quantity}`)
                    document.getElementById('quantity').innerHTML =
                        `<input type="number" min="1" max="${quantity}" value="${quantity}" class="form-control form-control-solid delete_quantity" data-id="${item}" >`
                    $('#quantity input').change(function() {
                        console.log($(this))
                        Quantity(a, id, $(this))
                    })
                })

                function Quantity(a, id, input) {
                    var val = input.val()
                    a.attr('href', `/public/admin/delete-item/${id}/${val}`)
                }

                $(document).ready(function() {
                    $('#add').click(function() {
                        $('#item').trigger('change')
                        $('#addon').trigger('change')
                        $('input[type="radio"]').click(function() {
                            console.log($(this))
                            if ($(this).prop('checked')) {
                                $(this).prop('checked', false)
                            }
                        })
                    })
                    $('#item').change(function() {
                        var data = $(this).val();
                        $.ajax({
                            url: '{{ url('/admin/get-addons ') }}',
                            data: {
                                data
                            },
                            success: function(data) {
                                var addoncategory = data.addon_categories
                                if (addoncategory.length > 0) {
                                    var adons = document.querySelector('.addons')
                                    adons.innerHTML = ``
                                    var div = document.querySelector('.addonCategory')
                                    div.innerHTML =
                                        `<label class=" col-lg-3 required form-label">Addons</label><div class="col-lg-9" id="addon"></div>`
                                    var addonDiv = document.querySelector('#addon')
                                    addoncategory.map(function(value, index) {
                                        var name = value.name
                                        if (value.addons.length > 0) {
                                            var html =
                                                `<div><span class="mb-2"><span class="category mb-3  rounded text-white" style="font-size:12px" data-id="${value.id}" ><i class="bi bi-cart-plus-fill me-1"></i></span>${value.name}</span></div>`
                                            if (value.type == 'MULTIPLE') {
                                                var type = "checkbox"
                                                var name = 'multiple[]'
                                            } else {
                                                var type = "radio"
                                                var name = name
                                            }
                                            html +=
                                                `<div id='name${value.id}' class="ms-6" style="display:none">`
                                            value.addons.map(function(value, index) {
                                                var input =
                                                    `<input class="option mt-2 mb-2" data-clicks="${2}" type="${type}" name="${name}" id="${name}" value="${value.id}"/> <span class="badge bg-warning">${value.name} ${value.price}</span>`
                                                html += input
                                            })
                                            html += `</div>`
                                            addonDiv.innerHTML += html
                                        }
                                    })
                                } else {
                                    console.log('else')
                                    var div = document.querySelector('.addonCategory')
                                    div.innerHTML = ``
                                    var div = document.querySelector('.addons')
                                    div.innerHTML = ``
                                }
                                $('input[type="radio"]').click(function() {
                                    let data = $(this).data('clicks')
                                    if (data % 2 != 0) {
                                        $(this).prop('checked', false)
                                    }
                                    $(this).data('clicks', data + 1)
                                })
                                $('.category').click(function() {
                                    let id = $(this).data('id')
                                    $('#name' + id).toggle()
                                })
                            }
                        })
                    })
                    $('#prevent').click(function(event) {
                        if ($('.option').length > 0) {
                            if ($('.option[type="radio"]:checked').length > 0 || $(
                                    '.option[type="checkbox"]:checked')
                                .length > 0) {
                                $(this).trigger('click')
                            } else {
                                event.preventDefault();
                                document.getElementById('cart-addon').style.display = 'block';
                                document.getElementById('cart-addon').style.color = "red";
                                document.getElementById('cart-addon').classList.add("apply-shake");
                                setTimeout(RemoveClass, 1000);
                            }
                        }
                    })
                })

                function RemoveClass() {
                    document.getElementById('cart-addon').style.display = 'none';
                    document.getElementById('cart-addon').classList.remove("apply-shake");
                    document.getElementById('cart-addon').style.color = "black";
                }

                $(".xzoom").click(function(e) {
                    e.preventDefault();
                    let id = $(this).attr("data-id");
                    $("#xzoom-" + id).toggleClass('zoomed');
                });
            </script>

            <!-- End Add Item Model  -->
        </div>
    </div>
    </div>
    <style>
        @keyframes shake {

            10%,
            90% {
                transform: translate3d(-1px, 0, 0);
            }

            20%,
            80% {
                transform: translate3d(2px, 0, 0);
            }

            30%,
            50%,
            70% {
                transform: translate3d(-4px, 0, 0);
            }

            40%,
            60% {
                transform: translate3d(4px, 0, 0);
            }
        }

        .apply-shake {
            animation: shake 0.82s cubic-bezier(.36, .07, .19, .97) both;
        }

        .zoomed {
            transform: scale(4);
            z-index: 99999999;
        }
    </style>
@endsection
