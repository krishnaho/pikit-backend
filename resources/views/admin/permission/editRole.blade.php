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
    <div class="card card-bordered card-preview  mb-5 ">
        <div class="card-inner">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Edit Role</span>
            </h3>
        </div>
    </div>


    <div class="d-flex flex-column ">
        <div class="content flex-column-fluid" id="kt_content">
            <!--begin::Layout-->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card card-bordered card-preview p-4">
                        <div class="card-body">
                            <div class="tab-pane fade show active">
                                <form action="{{ route('admin.updateRole', $role->id) }}" method="POST" class="form mt-5">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $role->id }}">
                                    <div class='form-group row mb-4'>
                                        <label class=" col-lg-3 form-label"> Role Name:</label>
                                        <div class="col-lg-9">
                                            <span class="fw-bold bagde bg-info text-white p-3"
                                                style="border-radius:8px ">{{ $role->name }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-lg-3 required form-label">Permissions:</label>
                                        <div class="col-lg-9 text-end">
                                            <a type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-title="Double click to check all permissions"
                                                class="btn btn-sm btn-light btn-light-success" id="checkAll">
                                                Check All</a>
                                            <a type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-title="Double click to uncheck all permissions"
                                                class="btn btn-sm btn-light btn-light-danger" id="unCheckAll">
                                                Un-check All</a>
                                        </div>
                                    </div>
                                    <div class='form-group row mb-4'>
                                        <div class="assigning-checkboxes">
                                            <div class="col-lg-12">
                                                <div
                                                    class="form-group form-group-feedback form-group-feedback-right search-box mb-4">
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
                                                            <div class="form-group row  col-lg-12 mt-3 mb-3">
                                                                <div class="">
                                                                    @foreach ($permissions as $permission)
                                                                        @if ($permission->permission_head->id == $permissionHead->id)
                                                                            <label class="mx-1 mb-2">
                                                                                <input type="checkbox"
                                                                                    value="{{ $permission->name }}"
                                                                                    data-name="{{ $permission->readable_name }}"
                                                                                    @if (in_array($permission->id, $rolePermissionIds)) checked="checked" @endif
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
                                    <div class="mt-3 text-end">
                                        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>
                                            Update Role</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($role->users->count() != 0)
                    <div class="col-lg-4">
                        <div class="card card-bordered card-preview  mb-5 ">
                            <div class="card-inner">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bolder fs-3 mb-1">Users Assigned</span>
                                    <span class="text-gray-600 fs-6 ms-1">( {{ $role->users->count() }} )</span>
                                </h3>
                            </div>
                            <div class="card-body">
                                <table id="" class="table align-middle gs-0 gy-4">
                                    <thead>
                                        <tr class="fw-bolder text-muted bg-light">
                                            <th class="ps-2 text-center rounded-start">Id</th>
                                            <th class="ms-3">User</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($role->users as $user)
                                            <tr class="">
                                                <td class="text-center ps-2">{{ $user->id }}</td>
                                                <td class="">
                                                    <div class="d-flex flex-row align-items-center">
                                                        <div class="p-2" style="border-radius:100px;">
                                                            @if ($user->image)
                                                                <img src="{{ asset($user->image) }}" class="img-thumbnail"
                                                                    alt="..."
                                                                    style="width:4vw;height:4vw;border-radius:100px; object-fit:cover;">
                                                            @else
                                                                <img src="{{ asset('/assets/images/propic.jpg') }}"
                                                                    class="img-thumbnail" alt="..."
                                                                    style="width:4vw;height:4vw;border-radius:100px; object-fit:cover;">
                                                            @endif
                                                        </div>
                                                        <div class="d-flex flex-column">
                                                            <span class="fw-bold">{{ $user->name }}</span>
                                                            <span class="fw-bold text-muted">{{ $user->email }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-lg-4">
                        <div class="content">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-inner">
                                        <h6 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bolder  mb-1">No users found with the role
                                                of</span>
                                            <span class="text-gray-600 ms-1">( {{ $role->name }} )</span>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
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
@endsection
