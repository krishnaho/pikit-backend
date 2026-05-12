@extends('admin.layout.master')
@section('title')
    Permission
@endsection
@section('content')
    <style>
        .assigning-checkboxes label {
            margin-right: 10px;
            background-color: rgba(250, 250, 250, 0.3);
            border-radius: 8px;
            margin-bottom: 1.2rem;
        }

        .assigning-checkboxes label:hover {
            cursor: pointer;
        }

        .assigning-checkboxes label span {
            text-align: center;
            display: block;
            padding: 4px 7.5px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        }

        .assigning-checkboxes label input {
            position: absolute;
            top: -20px;
            display: none;
        }

        .assigning-checkboxes input:checked+span {
            background-color: #2ebf91;
            padding: 8px 15px;
            color: #fff;
            border: 1px solid #e0e0e0;
        }
    </style>
    <div class="nk-content-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-bordered card-preview  mb-5 ">
                    <div class="card-inner">
                        <div class="card-title-group">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 mb-1">View Permissions</span>
                            </h3>
                            <div class="card-tools">
                                <button type="button"
                                    class="btn btn-sm btn-outline btn-outline-dashed btn-outline-dark btn-active-light-dark me-4"
                                    data-kt-menu-placement="bottom-end" data-bs-toggle="modal" data-bs-target="#addNewRole">
                                    <i class="fa fa-plus text-dark"></i> Add New Role
                                </button>
                                <button type="button"
                                    class="btn btn-sm btn-outline btn-outline-dashed btn-outline-dark btn-active-light-dark me-4"
                                    data-kt-menu-placement="bottom-end" data-bs-toggle="modal"
                                    data-bs-target="#addNewPermission">
                                    <i class="fa fa-plus text-dark"></i> Add New Permission
                                </button>
                                <button type="button"
                                    class="btn btn-sm btn-light btn-outline btn-outline-dashed btn-outline-dark btn-active-light-info"
                                    data-kt-menu-placement="bottom-end" data-bs-toggle="modal"
                                    data-bs-target="#viewAllPermissions">View
                                    All Permission
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-5 g-xl-9">

                    @foreach ($roles as $role)
                        <div class="col-md-4">
                            <div class="card card-bordered card-preview  mb-5">
                                <div class="card-inner">
                                    <div class="card-title">
                                        <h2>{{ $role->name }}</h2>
                                    </div>

                                    <div class="card-body pt-1">
                                        <div class="fw-bolder text-gray-600">
                                            Total users with this role: {{ $role->users->count() }}
                                        </div>

                                        <div class="text-gray-600 py-1">
                                            @foreach ($role->permissions as $key => $rolePermission)
                                                @if ($key < 5)
                                                    <div>
                                                        <em class="icon ni ni-dot"></em>
                                                        {{ $rolePermission->readable_name }}
                                                    </div>
                                                @endif
                                            @endforeach
                                            @if ($role->permissions->count() > 5)
                                                <div class="d-flex align-items-center ">
                                                    <span class="bullet bg-info me-3"></span>
                                                    <em>and {{ $role->permissions->count() - 5 }} more...</em>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex-wrap pt-0 text-center">
                                            <a href="{{ route('admin.editRole', $role->id) }}"
                                                class="btn btn-light btn-light-info my-1">Edit
                                                Role</a>
                                            <button type="button" class="btn btn-light btn-light-danger my-1"
                                                data-bs-toggle="modal" data-bs-target="#delete{{ $role->id }}">
                                                Delete Role
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @foreach ($roles as $role)
        <div class="modal fade" tabindex="-1" id="delete{{ $role->id }}">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><span class="font-weight-bold">Do you want to delete Role?
                            </span></h5>
                        <div class="btn btn-icon btn-sm btn-active-light-info ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <span class="svg-icon svg-icon-2x">&times;</span>
                        </div>
                    </div>
                    <div class="modal-body">
                        <span class="help-text text-theme-6 text-sm">This Role will be permanently deleted . Do you wish
                            to
                            delete?</span>
                    </div>
                    <div class=" modal-footer">
                        <a href="javascript:;" type="button" class="btn btn-light" data-bs-dismiss="modal"> No </a>
                        <a href="{{ route('admin.deleteRole', $role->id) }}" class="btn btn-danger text-white">Yes</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="modal fade" tabindex="-1" id="addNewRole">
        <div class="modal-dialog modal-dialog-top modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Role</h5>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-info ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-2x">&times;</span>
                    </div>
                    <!--end::Close-->
                </div>
                <form action="{{ route('admin.createNewRole') }}" method="POST" class="form ">
                    @csrf
                    <div class="modal-body">
                        <div class="tab-pane fade show active">
                            <input type="hidden" name="id" value="">
                            <div class='form-group row mb-2'>
                                <label class=" col-lg-3 required form-label"> Role Name:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" placeholder="Enter Role Name" name='name'
                                        required />
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label class="col-lg-3 required form-label">Permissions:</label>
                                <div class="col-lg-9 text-end">
                                    <span style="font-size: 11px;font-weight:500;margin-right:5px">(Double Click To
                                        Active)</span>
                                    <a type="" class="btn btn-sm btn-light btn-light-success" id="checkAll">
                                        Check All</a>
                                    <a type="" class="btn btn-sm btn-light btn-light-danger" id="unCheckAll">
                                        Un-check All</a>
                                </div>
                            </div>
                            <div class='form-group row'>
                                <div class="assigning-checkboxes">
                                    <div class="col-lg-12">
                                        <div class="form-group form-group-feedback form-group-feedback-right search-box ">
                                            <input type="text" class="form-control form-control-lg search-input"
                                                placeholder="Filter with permission name..."
                                                style="border: 1px solid #ccc; box-shadow: none; height: 2.2rem;">
                                            <div class="form-control-feedback form-control-feedback-lg">
                                                <i class="icon-search4"></i>
                                            </div>
                                        </div>
                                        <div class="row">
                                            @foreach ($permissionHeads as $permissionHead)
                                                <div class="form-group column col-lg-6">
                                                    <div class="row text-center" style="margin: 0px 1px">
                                                        <div class="btn btn-sm btn-primary fs-5 fw-bold">
                                                            {{ $permissionHead->name }}
                                                        </div>
                                                    </div>
                                                    <div class="form-group row  col-lg-12 mt-2 mb-2">
                                                        <div class="">
                                                            @foreach ($permissions as $permission)
                                                                @if ($permission->permission_head && $permission->permission_head->id == $permissionHead->id)
                                                                    <label class="mr-2 mb-2">
                                                                        <input type="checkbox"
                                                                            value="{{ $permission->name }}"
                                                                            name="permission[]">
                                                                        <span>{{ $permission->readable_name }}</span>
                                                                    </label>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>
                            Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="addNewPermission">
        <div class="modal-dialog modal-dialog-top modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Permission</h5>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-info ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span class="svg-icon svg-icon-2x">&times;</span>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="tab-pane fade show active">
                    <form action="{{ route('admin.createNewPermission') }}" method="POST" class="form mt-5">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="id" value="">
                            <div class='form-group row mb-4'>
                                <label class=" col-lg-4 required form-label"> Permission Name:</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" placeholder="Enter Permission Name"
                                        name='name' required />
                                </div>
                            </div>
                            <div class='form-group row mb-4'>
                                <label class=" col-lg-4 required form-label"> Permission Readable Name:</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control"
                                        placeholder="Enter Permission Readable Name" name='readable_name' required />
                                </div>
                            </div>
                            <div class='form-group row mb-4'>
                                <label class=" col-lg-4 required form-label"> Permission Head:</label>
                                <div class="col-lg-8">
                                    <select class="form-select js-select2" data-search="on" data-control="select2"
                                        name="permission_head_id" id="permission_head" required>
                                        @foreach ($permissionHeads as $permissionHead)
                                            @if ($permissionHead->id)
                                                <option value="{{ $permissionHead->id }}">
                                                    {{ $permissionHead->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer mt-3 float-end">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>
                                Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="viewAllPermissions">
        <div class="modal-dialog modal-dialog-top modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View All Permissions </h5>
                    <div class="btn btn-icon btn-sm btn-active-light-info ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span class="svg-icon svg-icon-2x">&times;</span>
                    </div>
                </div>
                <div class="modal-body">
                    <table class="table" data-auto-responsive="false">
                        <thead>
                            <tr class="fw-bolder text-muted text-center bg-light">
                                <th class="nk-tb-col">Id</th>
                                <th class="nk-tb-col tb-col-lg">Name</th>
                                <th class="nk-tb-col tb-col-lg">Permission Head</th>
                                <th class="nk-tb-col tb-col-lg">Relable Name</th>
                                <th class="nk-tb-col tb-col-lg">Created At</th>
                                <th class="nk-tb-col tb-col-lg"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissions as $permission)
                                <tr class="text-center">
                                    <td>{{ $permission->id }}</td>
                                    <td>{{ $permission->name }}</td>
                                    @if ($permission->permission_head)
                                        <td>{{ $permission->permission_head->name }}</td>
                                    @endif
                                    <td>{{ $permission->readable_name }}</td>
                                    <td>{{ $permission->created_at->diffForHumans() }}</td>
                                    <td class="">
                                        <div class="btn-group" aria-label="Basic example">
                                            <a href="{{ route('admin.editPermission', $permission->id) }}"
                                                class="btn btn-icon btn-info btn-active-color-light btn-sm ">
                                                <em class="icon ni ni-edit"></em>
                                            </a>
                                            <button type="button" id="deletePermission{{ $permission->id }}"
                                                class="btn btn-icon btn-danger btn-active-color-light btn-sm delete-permission"
                                                data-id="{{ $permission->id }}">
                                                <em class="icon ni ni-trash-fill"></em>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class='modal-footer'>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    </div>
    <script>
        $(function() {
            $('.assigning-checkboxes label').each(function() {
                $(this).attr('data-name', $(this).text().toLowerCase());
            });

            $('.search-input').on('keyup', function() {
                var searchTerm = $(this).val().toLowerCase();
                $('.assigning-checkboxes label').each(function() {
                    if ($(this).filter('[data-name *= ' + searchTerm + ']').length > 0 || searchTerm
                        .length < 1) {
                        $(this).show();
                        $
                    } else {
                        $(this).hide();
                    }
                });
            });

            $('#checkAll').dblclick(function(event) {
                $("input:checkbox").prop("checked", true);
            });
            $('#unCheckAll').dblclick(function(event) {
                $("input:checkbox").prop("checked", false);
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            $(document).on('click', '.delete-permission', function(e) {
                let id = $(this).data('id');
                Swal.fire({
                    html: `This Permission will be permanently deleted. Do you wish to delete?`,
                    icon: "error",
                    buttonsStyling: false,
                    showCancelButton: true,
                    confirmButtonText: 'No',
                    cancelButtonText: '<a class="btn-light-success text-white" href="{{ route('admin.deletePermission', '') }}/' +
                        id + '">Yes</a>',
                    customClass: {
                        cancelButton: 'btn btn-danger',
                        confirmButton: "btn btn-active-light-dark"
                    }
                });
            });
        })
    </script>
@endsection
