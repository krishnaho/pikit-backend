@extends('admin.layout.master')
@section('title')
    Permission
@endsection
@section('content')
    <div class="card mb-5 mb-xl-8">
        <div class="card-header border-0 ">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Edit Permission</span>
            </h3>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.updatePermission') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $permission->id }}">
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Name:</label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control form-control-lg" name="name"
                            value="{{ $permission->name }}" placeholder="Enter Permissions Name" required
                            id="permissionName">
                    </div>
                </div>
                <div class="form-group row mt-4">
                    <label class="col-lg-3 col-form-label">Readable Name:</label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control form-control-lg" name="readable_name"
                            value="{{ $permission->readable_name }}" placeholder="Enter Permissions Readble Name" required
                            id="permissionReadableName">
                    </div>
                </div>
                <div class='form-group row mt-4'>
                    <label class=" col-lg-3 required form-label"> Permission Head:</label>
                    <div class="col-lg-9">
                        <select name="permission_head_id" id="permission_head" class="form-control form-control-lg"
                            data-control="select2" required>
                            @foreach ($permissionHeads as $permissionHead)
                                <option value="{{ $permissionHead->id }}" @if ($permissionHead->id == $permission->permission_head->id) selected @endif>
                                    {{ $permissionHead->name }}</option>
                            @endforeach
                        </select>

                    </div>
                </div>
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        Update Permission
                        <i class="icon-database-insert ml-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
