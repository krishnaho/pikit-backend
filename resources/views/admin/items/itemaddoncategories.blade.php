@extends('admin.layout.master')
@section('title')
    Item Addon Category
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">Item Addon Category</h4>
                </div>
            </div>
            <div class="nk-block-head-content">
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="card-content">
                        <div class="card-header mb-4">
                            <h5 class="card-title">Update Item Addon Category <span class="badge bg-info"> {{ $item->name }}</span></h5>
                        </div>
                        <form action="{{ route('admin.updateItemAddonCategory') }}" method="post"
                            enctype="multipart/form-data">
                            <div class="card-body">
                                @csrf
                                <input type="hidden" name="id" value="{{ $item->id }}">
                                <div class="form-group row">
                                    <label class="col-md-3 form-label">Selected Item Name</label>
                                    <div class="col-md-9">
                                        <input type="text" name="name" class="form-control"
                                            value="{{ $item->name }}" placeholder="Item Name" disabled>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 form-label"> Addon Categories</label>
                                    <div class="col-md-9">
                                        <select class="form-select js-select2" data-control="select2"
                                            name="addonCategories[]" data-placeholder="Select an option"
                                            data-allow-clear="true" multiple="multiple">
                                            <option value="" disabled>Select an Option</option>
                                            @foreach ($addoncategories as $category)
                                                <option value="{{ $category->id }}"
                                                    @if (in_array($category->id, $selectedCategories)) selected @endif>
                                                    {{ $category->name }}

                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-success"><i class="fe-upload"></i>
                                    Update </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
