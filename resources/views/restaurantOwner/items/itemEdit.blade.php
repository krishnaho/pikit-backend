@extends('admin.layout.master')
@section('title')
    Edit Item
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">Edit Item</h4>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="card-content">
                        <div class="card-header">
                            <h5 class="card-title">Update Item</h5>
                        </div>
                        <form action="{{ route('restaurantOwner.updateItem') }}" method="POST" enctype="multipart/form-data"
                            id="myForm">
                            @csrf
                            <div class="card-body ">
                                <input type="hidden" name='id' value="{{ $item->id }}" required />
                                <div class='form-group row mb-1 align-middle'>
                                    <div class="col-lg-6 mb-1">
                                        <label for='name' class="required form-label">Name</label>
                                        <input type="text" class="form-control" placeholder="Name" name='name'
                                            id='name' value="{{ $item->name }}" required />
                                    </div>
                                    <div class="col-lg-6 mb-1">
                                        <label name='image' for='image' class="required form-label">Image</label>
                                        <input type="file" class="form-control" placeholder="Image" name='image'
                                            id='image' />
                                    </div>
                                    <div class="col-lg-12 mb-1">
                                        <label for='current_image' class="required form-label">Current Image</label>
                                        <div>
                                            @if ($item->image)
                                                <img src="{{ asset($item->image) }}" id='current_image'
                                                    style="height:6vw;object-fit:contain;border-radius:5px; " />
                                            @else
                                                <span class="badge badge-dot bg-info" id="current_image">No Image
                                                    Found</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-1">
                                        <label for='description' class="required form-label">Description</label>
                                        <input type="text" class="form-control" placeholder="Description"
                                            name='description' value="{{ $item->description }}" id='description' required />
                                    </div>
                                    <input type="hidden" name='restaurant_id' value="{{ $restaurant->id }}" />
                                    <div class="col-lg-6 mb-1">
                                        <label for='item_category_id' class="required form-label">Item
                                            Category</label>
                                        <select class="form-select js-select2" data-control="select2"
                                            data-placeholder="Select an option" id="item_category_id"
                                            name='item_category_id'>
                                            <option value="" disabled selected>Select An Option</option>
                                            @foreach ($itemCategories as $itemCategory)
                                                <option value="{{ $itemCategory->id }}"
                                                    @if ($item->item_category_id == $itemCategory->id) selected @endif>
                                                    {{ $itemCategory->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group row' id="withOutDiscount"
                                    @if ($item->market_price && $item->market_price > $item->selling_price) style= "display: none" @else style= "display: show" @endif>
                                    <div class="col-lg-6 mb-1">
                                        <label for='price' class="required form-label">Price</label>
                                        <input type="number" step="any" class="form-control" placeholder="Price"
                                            name='price' value="{{ $item->selling_price }}" id='price' required />
                                    </div>
                                    <div class="col-lg-6 align-end  mb-1">
                                        <button type="button" class="btn btn-secondary" value="ADD"
                                            onclick="changeType(this)">
                                            Add Discount
                                        </button>
                                    </div>
                                    <input type="hidden" id="check" name="check">
                                </div>
                                <div class='form-group row' id="withDiscount"
                                    @if ($item->market_price && $item->market_price > $item->selling_price) style= "display: show" @else style= "display: none" @endif>
                                    <div class="col-lg-6 mb-1">
                                        <label for='selling_price' class="required form-label">Selling
                                            Price</label>
                                        <input type="number" step="any" class="form-control"
                                            placeholder="Selling Price" value="{{ $item->selling_price }}"
                                            name='selling_price' id='selling_price' />
                                    </div>
                                    <div class="col-lg-5 mb-1">
                                        <label for='market_price' class="required form-label">Market
                                            Price</label>
                                        <input type="number" step="any" class="form-control"
                                            placeholder="Market Price" value="{{ $item->market_price }}"
                                            name='market_price' id='market_price' />
                                    </div>
                                    <div class="col-lg-1 align-end  mb-1">
                                        <button type="button" class="btn btn-secondary" value="REMOVE"
                                            onclick="changeType(this)">
                                            -
                                        </button>
                                    </div>
                                </div>
                                <div class='form-group row mb-4 align-middle'>
                                    <div class="col-lg-6 mb-1">
                                        <label for='min_quantity' class="required form-label">Min Quantity</label>
                                        <input type="number" class="form-control" placeholder="Min Quantity"
                                            name='min_quantity' value="{{ $item->min_quantity }}" id='min_quantity' />
                                    </div>
                                    <div class="col-lg-6 mb-1">
                                        <label for='max_quantity' class="required form-label">Max Quantity</label>
                                        <input type="number" class="form-control" placeholder="Max Quantity"
                                            name='max_quantity' value="{{ $item->max_quantity }}" id='max_quantity' />
                                    </div>
                                    <div class="col-lg-6 mb-1">
                                        <label for='commision_rate' class="required form-label">Commision</label>
                                        <input type="number" step="any" class="form-control"
                                            placeholder="Commision" value="{{ $item->commision_rate }}"
                                            name='commision_rate' id='commision_rate' />
                                    </div>
                                    <div class="col-lg-12  mt-4">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="is_popular"
                                                id="is_popular" @if ($item->is_popular) checked @endif>
                                            <label class="custom-control-label" for="is_popular">Is
                                                Popular</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12  mt-4">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="is_recommended"
                                                id="is_recommended" @if ($item->is_recommended) checked @endif>
                                            <label class="custom-control-label" for="is_recommended">Is
                                                Recommended</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12  mt-4">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="is_veg"
                                                id="is_veg" @if ($item->is_veg) checked @endif>
                                            <label class="custom-control-label" for="is_veg">Is Veg</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
