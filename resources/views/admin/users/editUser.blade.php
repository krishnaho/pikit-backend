@extends('admin.layout.master')
@section('title')
    Edit User
@endsection
@section('content')
    <div class="card mb-5 mb-xxl-8">
        <div class="card-body pt-9 pb-0">
            <div class="d-flex flex-wrap flex-sm-nowrap">
                <div class="me-7 mb-4">
                    <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                        @if ($user->image)
                            <img src="{{ asset($user->image) }}"
                                style="width:8vw;height:8vw;object-fit:cover;border-radius:5px" alt="image">
                        @else
                            <img src="https://www.pngitem.com/pimgs/m/35-350426_profile-icon-png-default-profile-picture-png-transparent.png"
                                style="width:8vw;height:8vw;object-fit:cover;border-radius:5px" alt="image">
                        @endif
                    </div>
                </div>

                <div class="flex-grow-1 ms-2">
                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center mb-2">
                                <span class="text-gray-900  fs-2 fw-bolder me-1">{{ $user->name }}</span>
                                <span>
                                    <span class="svg-icon svg-icon-1 svg-icon-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z"
                                                fill="#00A3FF"></path>
                                            <path class="permanent"
                                                d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z"
                                                fill="white"></path>
                                        </svg>
                                    </span>
                                </span>
                            </div>

                            <div class="d-flex flex-wrap fw-bold fs-6 mb-4 pe-2">
                                <span class="d-flex align-items-center text-gray-400  me-5 mb-2">
                                    <span class="svg-icon svg-icon-4 me-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3"
                                                d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 7C10.3 7 9 8.3 9 10C9 11.7 10.3 13 12 13C13.7 13 15 11.7 15 10C15 8.3 13.7 7 12 7Z"
                                                fill="black"></path>
                                            <path
                                                d="M12 22C14.6 22 17 21 18.7 19.4C17.9 16.9 15.2 15 12 15C8.8 15 6.09999 16.9 5.29999 19.4C6.99999 21 9.4 22 12 22Z"
                                                fill="black"></path>
                                        </svg>
                                    </span>
                                    @foreach ($user->roles as $role)
                                        {{ $role->name }}
                                    @endforeach
                                </span>
                                @if ($user->phone)
                                    <span class="d-flex align-items-center text-gray-400  me-5 mb-2">
                                        <span class="svg-icon svg-icon-4 me-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z" />
                                            </svg>
                                        </span> {{ $user->phone }}
                                    </span>
                                @else
                                    <span></span>
                                @endif
                                @if ($user->email)
                                    <span class="d-flex align-items-center text-gray-400  mb-2">
                                        <span class="svg-icon svg-icon-4 me-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3"
                                                    d="M21 19H3C2.4 19 2 18.6 2 18V6C2 5.4 2.4 5 3 5H21C21.6 5 22 5.4 22 6V18C22 18.6 21.6 19 21 19Z"
                                                    fill="black"></path>
                                                <path
                                                    d="M21 5H2.99999C2.69999 5 2.49999 5.10005 2.29999 5.30005L11.2 13.3C11.7 13.7 12.4 13.7 12.8 13.3L21.7 5.30005C21.5 5.10005 21.3 5 21 5Z"
                                                    fill="black"></path>
                                            </svg>
                                        </span> {{ $user->email }}
                                    </span>
                                @else
                                    <span>Email</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <ul class="nav nav-tabs mt-3 ">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#general">
                        <em class="icon ni ni-user"></em><span>General</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#userbank">
                        <em class="icon ni ni-building-fill"></em><span>Bank Details</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#rolespermission">
                        <em class="icon ni ni-link"></em><span>Roles and permissions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#userorders">
                        <em class="icon ni ni-truck"></em><span>Orders</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#userwallet">
                        <em class="icon ni ni-wallet-fill"></em><span>Wallet</span>
                    </a>
                </li>
            </ul>
            <div class="tab-content mt-0">
                <div class="tab-pane active" id="general">
                    <div id="kt_account_settings_profile_details" class="collapse show">
                        <form action="{{ route('admin.updateUser') }}" method="post" enctype="multipart/form-data"
                            class="form-validate">
                            @csrf
                            <input type="hidden" name="id" value="{{ $user->id }}">
                            <div class="card-body mt-3">
                                <div class="row g-3 align-center">
                                    <div class="col-lg-5">
                                        <div class="form-group"><label class="form-label" for="name">
                                                User Name</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="form-group">
                                            <div class="form-control-wrap">
                                                <input type="text" name="name" class="form-control"
                                                    placeholder="Name" value="{{ $user->name }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 align-center mt-1">
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <label class="form-label" for="name">
                                                Current Image</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        @if ($user->image)
                                            <img src="{{ asset($user->image) }}"
                                                style="width:4vw;height:4vw;object-fit:cover;border-radius:5px; " />
                                        @else
                                            <span class="badge badge-dot bg-info">No Image Found</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row g-3 align-center mt-1">
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <label class="form-label" for="name">
                                                Image</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="form-group">
                                            <div class="form-control-wrap">
                                                <input type="file" name="image" class="form-control"
                                                    placeholder="Select Image">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 align-center mt-1">
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <label class="form-label" for="email"> Email</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="form-group">
                                            <div class="form-control-wrap">
                                                <input type="text" name="email" class="form-control"
                                                    placeholder="Email" value="{{ $user->email }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 align-center mt-1">
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <label class="form-label" for="address"> Address</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="form-group">
                                            <div class="form-control-wrap">
                                                <input type="text" name="address" class="form-control"
                                                    placeholder="Address" value="{{ $user->address }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 align-center mt-1">
                                    <div class="col-lg-5">
                                        <div class="form-group"><label class="form-label" for="name">
                                                Phone</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="form-group">
                                            <div class="form-control-wrap">
                                                <input type="tel" class="form-control" placeholder="987654321"
                                                    name="phone" minlength="9" maxlength="9"
                                                    value="{{ $user->phone }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-lg btn-primary">
                                        Update
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                <div class="tab-pane" id="userbank">
                    <div id="kt_account_settings_profile_details" class="collapse show">
                        <form class="form fv-plugins-bootstrap5 fv-plugins-framework"
                            action="{{ route('admin.updateBankDetails') }}" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{ $user->id }}">

                            <div class="card-body mt-3">
                                <div class="row g-3 mb-2 align-center">
                                    <label class=" col-lg-5 form-label" for="holder_name">Holder Name</label>
                                    <div class="col-lg-7 fv-row fv-plugins-icon-container">
                                        <input type="text" name="holder_name" class="form-control" id="holder_name"
                                            placeholder="Holder Name" value="{{ $user->holder_name }}">
                                    </div>
                                </div>
                                <div class="row g-3 mb-2 align-center">
                                    <label class=" col-lg-5 form-label" for="account_number">Account Number</label>
                                    <div class="col-lg-7 fv-row fv-plugins-icon-container">
                                        <input type="number" name="account_number" id="account_number"
                                            class="form-control" placeholder="Account Number"
                                            value="{{ $user->account_number }}">
                                    </div>
                                </div>
                                <div class="row g-3 mb-2 align-center">
                                    <label class=" col-lg-5 form-label" for="ifsc_code"> IFSC Code</label>
                                    <div class="col-lg-7 fv-row fv-plugins-icon-container">
                                        <input type="text" name="ifsc_code" id="ifsc_code" class="form-control"
                                            placeholder="IFSC Code" value="{{ $user->ifsc_code }}">
                                    </div>
                                </div>
                                <div class="row g-3 mb-2 align-center">
                                    <label class=" col-lg-5 form-label" for="bank_name">Bank Name</label>
                                    <div class="col-lg-7 fv-row fv-plugins-icon-container">
                                        <input type="text" name="bank_name" class="form-control"
                                            placeholder="Bank Name" value="{{ $user->bank_name }}">
                                    </div>
                                </div>

                                <div class="form-group d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-lg btn-primary">
                                        Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="tab-pane" id="rolespermission">
                    <div id="kt_account_settings_profile_details" class="collapse show">
                        <form class="form fv-plugins-bootstrap5 fv-plugins-framework"
                            action="{{ route('admin.updateUserRole') }}" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{ $user->id }}">
                            <div class="card-body mt-3">
                                <div class="row g-3 align-center">
                                    <label class=" col-lg-5 form-label" for="password"> Change Password</label>
                                    <div class="col-lg-7 fv-row fv-plugins-icon-container">
                                        <input type="password" name="password" class="form-control"
                                            placeholder="Password" minlength="8">
                                    </div>
                                </div>
                                <div class="row g-3 align-center mt-1">
                                    <label class=" col-lg-5 form-label" for="password"> Current Role</label>
                                    <div class="col-lg-7">
                                        @foreach ($user->roles as $role)
                                            <span class="badge bg-purple">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="row g-3 align-center mt-1">
                                    <label class=" col-lg-5 form-label" for="password"> Change Role</label>
                                    <div class="col-lg-7 fv-row fv-plugins-icon-container">
                                        <select class="form-select js-select2" data-search="on" data-control="select2"
                                            name="role">
                                            @foreach ($roles as $role)
                                                <option @if (in_array($role->id, $user->roles->pluck('id')->toArray())) selected @endif
                                                    value="{{ $role->id }}">
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-lg btn-primary">
                                        Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="tab-pane" id="userorders">
                    <div id="kt_account_settings_profile_details" class="collapse show">
                        <div class="card-body mt-3">
                            <table class="table card-bordered" id="users" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th class="col">#</th>
                                        <th class="col">Order Status</th>
                                        <th class="col">Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($orders !== null && $orders->count() > 0)
                                        @foreach ($orders as $order)
                                            <tr>
                                                <th class="col">{{ $order->unique_order_id }}</th>
                                                <th class="col">
                                                    @if ($order->orderstatus)
                                                        <span class="badge bg-info">
                                                            {{ $order->orderstatus->name }}
                                                        </span>
                                                    @endif
                                                </th>
                                                <th class="col">{{ $order->created_at->diffForHumans() }}</th>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="text-center">
                                            <th colspan="5">
                                                No Data Found
                                            </th>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="userwallet">
                    <div id="kt_account_settings_profile_details" class="collapse show">
                        <div class=" pt-4 mb-6 mb-xl-9">
                            <div class="border-0 mx-5 d-flex justify-content-between">
                                <div class="card-title">
                                    <h2 class="fw-bolder">Wallet Balance</h2>
                                    <div class="fs-2 fw-bolder">{{ $user->balance ?? '0.00' }} ₹
                                    </div>
                                </div>
                                <div class="me-5">
                                    <span href="#" class="btn bg-purple text-white" data-bs-toggle="modal"
                                        data-bs-target="#kt_modal_adjust_balance">
                                        <span class="svg-icon svg-icon-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3"
                                                    d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z"
                                                    fill="white" />
                                                <path
                                                    d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z"
                                                    fill="white" />
                                            </svg>
                                        </span>
                                        <div>
                                            Adjust Balance
                                        </div>
                                    </span>
                                </div>
                            </div>
                            <div class="modal fade" id="kt_modal_adjust_balance" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered mw-650px modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h2 class="fw-bolder">Adjust Balance
                                            </h2>
                                            <div id="kt_modal_adjust_balance_close" data-bs-dismiss="modal"
                                                class="btn btn-icon btn-sm btn-active-icon-primary">
                                                <span class="svg-icon svg-icon-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none">
                                                        <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                            height="2" rx="1"
                                                            transform="rotate(-45 6 17.3137)" fill="black" />
                                                        <rect x="7.41422" y="6" width="16" height="2"
                                                            rx="1" transform="rotate(45 7.41422 6)"
                                                            fill="black" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                            <div class="d-flex text-center mb-9">
                                                <div class="w-50 border border-dashed border-gray-300 rounded mx-2 p-4">
                                                    <div class="fs-6 fw-bold mb-2 text-muted">
                                                        Current Balance</div>
                                                    <div class="fs-2 fw-bolder" kt-modal-adjust-balance="current_balance"
                                                        id="current_balance">{{ $user->balance ?? '0.00' }} ₹
                                                    </div>
                                                </div>
                                                <div class="w-50 border border-dashed border-gray-300 rounded mx-2 p-4">
                                                    <div class="fs-6 fw-bold mb-2 text-muted">
                                                        New Balance
                                                        <i class="bi bi-exclamation-circle text-black ms-1 fs-7"
                                                            data-bs-toggle="tooltip"
                                                            title="Enter an amount to preview the new balance."></i>
                                                    </div>
                                                    <div class="fs-2 fw-bolder" kt-modal-adjust-balance="new_balance"
                                                        id="updated_balance">{{ $user->balance ?? '0.00' }} ₹</div>
                                                </div>
                                            </div>
                                            <form id="kt_modal_adjust_balance_form" class="form"
                                                action="{{ route('admin.addMoneyToWallet') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $user->id }}">
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-bold form-label mb-2">Adjustment
                                                        Type</label>
                                                    <select class="form-select js-select2" data-control="select2" required
                                                        name="adjustment" aria-label="Select an option"
                                                        data-dropdown-parent="#kt_modal_adjust_balance"
                                                        data-placeholder="Select An Option" data-hide-search="true"
                                                        id="option">
                                                        <option value="" disabled selected>Select An Option</option>
                                                        <option value="deposit">
                                                            Deposit</option>
                                                        <option value="withdraw">
                                                            Withdraw</option>
                                                    </select>
                                                </div>
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-bold form-label mb-2">Amount</label>
                                                    <input id="add" type="text"
                                                        class="form-control form-control-solid" name="amount"
                                                        value="" required />
                                                </div>
                                                <div class="fv-row mb-7">
                                                    <label class="fs-6 fw-bold form-label mb-2">
                                                        Add Adjustment Note
                                                    </label>
                                                    <textarea class="form-control form-control-solid rounded-3 mb-5" name="message"></textarea>
                                                </div>
                                                <div class="text-center">
                                                    <button type="reset" data-bs-dismiss="modal"
                                                        class="btn btn-light me-3">
                                                        Discard
                                                    </button>
                                                    <button type="submit" id="kt_modal_adjust_balance_submit"
                                                        class="btn btn-primary">
                                                        <span class="indicator-label">Submit</span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive mb-4">
                                <table class="table card-bordered mt-3" id="users" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th class="col"> Transaction ID</th>
                                            <th class="col"> Type</th>
                                            <th class="col"> Amount</th>
                                            <th class="col"> Message</th>
                                            <th class="col"> Transaction Happened On</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-bold">
                                        @if ($user->transactions !== null && $user->transactions->count() > 0)
                                            @foreach ($user->transactions->reverse() as $transaction)
                                                <tr>
                                                    <td>
                                                        {{ $transaction->uuid }}
                                                    </td>

                                                    <td>
                                                        @if ($transaction->type == 'withdraw')
                                                            <span class="badge bg-info">
                                                                {{ $transaction->type }}</span>
                                                        @elseif($transaction->type == 'deposit')
                                                            <span class="badge bg-success">
                                                                {{ $transaction->type }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $transaction->amount }} ₹</td>


                                                    <td>
                                                        @if (isset($transaction->meta['description']))
                                                            {{ $transaction->meta['description'] }}
                                                        @else
                                                            No Message
                                                        @endif
                                                    </td>

                                                    <td>{{ $transaction->created_at->diffForHumans() }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr class="text-center">
                                                <th colspan="5">
                                                    No Data Found
                                                </th>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var input = document.getElementById('add');
        var select = document.getElementById('option');
        input.addEventListener('input', function(e) {
            var current_balance = parseFloat(document.getElementById('current_balance').innerText);
            var val = parseFloat(e.target.value)
            if (select.value == 'deposit') {
                if (isNaN(val)) {
                    document.getElementById('updated_balance').innerText = 0.00;
                } else {
                    document.getElementById('updated_balance').innerHTML =
                        `<span>${parseFloat(current_balance+val)}</span><span> ₹<em class="icon ni ni-arrow-long-up text-success"></em></span>`;
                }
            } else {
                console.log(val, current_balance, e.target.value)
                if (isNaN(val) || current_balance - parseFloat(e.target.value) < 0) {
                    document.getElementById('updated_balance').innerText = 0.00;
                } else {
                    document.getElementById('updated_balance').innerHTML =
                        `<span>${parseFloat(current_balance-val)}</span><span> ₹<em class="icon ni ni-arrow-long-down text-success"></em></span>`;
                }
            }
        })
    </script>

    <script type="text/javascript">
        function copyText() {
            let input = document.getElementById('input');
            navigator.clipboard.writeText(input.value);
            console.log(document.getElementById('copy').innerHTML = "<i class='bi bi-check fs-5'></i>");
        }

        $('#plan').click(() => {
            $("#change_plan").toggle(
                function() {
                    $("#change_plan").css({
                        "display": "block"
                    });
                },
                function() {
                    $("#change_plan").css({
                        "display": "hide"
                    });
                }
            );


            console.log("ok")
        })
        $("#dob").flatpickr({
            maxDate: new Date().fp_incr(-2190)
        });
    </script>
@endsection
