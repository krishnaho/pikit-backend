<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\imports\itemImport;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemGroup;
use App\Models\Restaurant;
use App\Models\RestaurantCategory;
use Exception;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class RestaurantItemController extends Controller
{
    public function viewItemCategories()
    {
        $restaurantIds = Auth::User()->restaurants->where('is_deleted', 0)->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();
        $itemCategories = ItemCategory::get();
        return view('restaurantOwner.itemCategories.itemCategories', [
            'itemCategories' => $itemCategories,
            'restaurant' => $restaurant
        ]);
    }

    public function addItemCategory(Request $request)
    {
        $itemCategory = new ItemCategory();
        $itemCategory->name = $request->name;
        $request->validate([
            'images' => 'image|mimes:jpeg,jpg,max:50',
        ]);
        if ($request->file('image')) {
            $file = $request->file('image');
            $imageName = time() . $file->getClientOriginalName();
            $file->move(public_path('/item-category/'), $imageName);
            $itemCategory->image = 'item-category/' . $imageName;
        }
        $itemCategory->description = $request->description;
        $itemCategory->save();
        return redirect()->back()->with('success', 'Item Category  Created Successfully');
    }

    public function updateItemCategory(Request $request)
    {
        $itemCategory = ItemCategory::where('id', $request->id)->first();
        $itemCategory->name = $request->name;
        $request->validate([
            'images' => 'image|mimes:jpeg,png,jpg,jfif,pjpeg,pjp,svg,webp|max:50',
        ]);
        if ($request->file('image')) {
            $file = $request->file('image');
            $imageName = time() . $file->getClientOriginalName();
            $file->move(public_path('/item-category/'), $imageName);
            @unlink(public_path($itemCategory->image));
            $itemCategory->image = 'item-category/' . $imageName;
        }
        $itemCategory->description = $request->description;
        $itemCategory->save();
        return redirect()->back()->with('success', 'Item Category Updated Successfully');
    }

    public function deleteItemCategory($id)
    {
        $itemCategory = ItemCategory::find($id);

        if ($itemCategory) {
            $itemCategory->delete();
            return redirect()->back()->with(['success' => 'Item Category Deleted Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }

    public function toggleItemCategory($id)
    {
        $itemCategory = ItemCategory::find($id);
        if ($itemCategory) {
            $itemCategory->toggleActive();
            $itemCategory->save();
            return redirect()->back()->with(['success' => 'Status Changed Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }



    public function viewItems()
    {
        $restaurantIds = Auth::User()->restaurants->where('is_deleted', 0)->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();
        $items = item::where('restaurant_id', $restaurant->id)->get();
        $itemCategories = ItemCategory::where('restaurant_id', $restaurant->id)->where('is_active', 1)->get();
        return view('restaurantOwner.items.items', [
            'items' => $items,
            'restaurant' => $restaurant,
            'itemCategories' => $itemCategories
        ]);
    }

    public function addItem(Request $request)
    {
        $item = new Item();
        $item->name = $request->name;
        $request->validate([
            'images' => 'image|mimes:jpeg,jpg,max:50',
        ]);
        if ($request->file('image')) {
            $file = $request->file('image');
            $imageName = time() . $file->getClientOriginalName();
            $file->move(public_path('/item/'), $imageName);
            $item->image = 'item/' . $imageName;
        }
        $item->item_category_id = $request->item_category_id;
        $item->description = $request->description;
        if ($request->check == 0) {
            $item->selling_price = $request->price;
            $item->market_price = 0;
        } else {
            $item->market_price = $request->market_price;
            $item->selling_price = $request->selling_price;
        }
        $item->commision_rate = $request->commision_rate;
        $item->min_quantity = $request->min_quantity;
        $item->max_quantity = $request->max_quantity;

        if ($request->is_popular == true) {
            $item->is_popular = 1;
        } else {
            $item->is_popular = 0;
        }
        if ($request->is_top_item == true) {
            $item->is_top_item = 1;
        } else {
            $item->is_top_item = 0;
        }
        if ($request->is_recommended == true) {
            $item->is_recommended = 1;
        } else {
            $item->is_recommended = 0;
        }
        if ($request->is_veg == true) {
            $item->is_veg = 1;
        } else {
            $item->is_veg = 0;
        }
        $item->save();
        return redirect()->back()->with('success', 'Item Category  Created Successfully');
    }

    public function editItem($id)
    {
        $restaurantIds = Auth::User()->restaurants->where('is_deleted', 0)->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();
        $item = Item::find($id);
        $itemCategories = ItemCategory::where('is_active', 1)->get();
        return view('restaurantOwner.items.itemEdit', [
            'item' => $item,
            'restaurant' => $restaurant,
            'itemCategories' => $itemCategories
        ]);
    }

    public function updateItem(Request $request)
    {
        $item = Item::where('id', $request->id)->first();
        $item->name = $request->name;
        $request->validate([
            'images' => 'image|mimes:jpeg,png,jpg,jfif,pjpeg,pjp,svg,webp|max:50',
        ]);
        if ($request->file('image')) {
            $file = $request->file('image');
            $imageName = time() . $file->getClientOriginalName();
            $file->move(public_path('/item/'), $imageName);
            @unlink(public_path($item->image));
            $item->image = 'item/' . $imageName;
        }
        $item->item_category_id = $request->item_category_id;
        $item->description = $request->description;
        if ($request->check == 0) {
            $item->selling_price = $request->price;
            $item->market_price = 0;
        } else {
            $item->market_price = $request->market_price;
            $item->selling_price = $request->selling_price;
        }
        $item->commision_rate = $request->commision_rate;
        $item->min_quantity = $request->min_quantity;
        $item->max_quantity = $request->max_quantity;

        if ($request->is_popular == true) {
            $item->is_popular = 1;
        } else {
            $item->is_popular = 0;
        }
        if ($request->is_top_item == true) {
            $item->is_top_item = 1;
        } else {
            $item->is_top_item = 0;
        }
        if ($request->is_recommended == true) {
            $item->is_recommended = 1;
        } else {
            $item->is_recommended = 0;
        }
        if ($request->is_veg == true) {
            $item->is_veg = 1;
        } else {
            $item->is_veg = 0;
        }
        $item->save();
        return redirect()->back()->with('success', 'Item Category Updated Successfully');
    }

    public function deleteItem($id)
    {
        $item = Item::find($id);

        if ($item) {
            $item->delete();
            return redirect()->back()->with(['success' => 'Item Category Deleted Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }

    public function toggleItem($id)
    {
        $item = Item::find($id);
        if ($item) {
            $item->toggleActive();
            $item->save();
            return redirect()->back()->with(['success' => 'Status Changed Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }
    public function viewItemAddonCategory($id)
    {
        $restaurantIds = Auth::User()->restaurants->where('is_deleted', 0)->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();
        $item = Item::where('restaurant_id', $restaurant->id)->with('addoncategories', 'restaurant.addoncategories')->find($id);
        $selectedCategories = $item->addoncategories->pluck('id')->toArray();
        $addonCategories = $item->restaurant->addoncategories;
        return view('restaurantOwner.items.itemaddoncategories', array(
            'item' => $item,
            'addoncategories' => $addonCategories,
            'selectedCategories' => $selectedCategories,

        ));
    }

    public function updateItemAddonCategory(Request $request)
    {
        try {
            $item = Item::where('id', $request->id)->first();
            $item->addoncategories()->sync($request->addonCategories);
            $item->save();
            return redirect()->back()->with(['success' => 'Item Addon Category Updated Successfully']);
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['error' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th]);
        }
    }

    public function getRestaurantItemCategories(Request $request)
    {
        $restaurantIds = Auth::User()->restaurants->where('is_deleted', 0)->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();
        $itemCategories = ItemCategory::where('is_deleted', 0)->where('restaurant_id', $restaurant->id)->get();
        return response()->json($itemCategories);
    }

    public function viewItemBulkUpload(Request $request)
    {

        if ($request->hasfile('csv')) {

            $path = $request->file('csv')->store('temp');
            $filepath = storage_path('app') . '/' . $path;
            $data = new itemImport;
            Excel::import($data, $filepath);
            if ($data->getRowCount() == 0) {
                return redirect()->back()->with(['error' => "You Didn't Upload Any Data In The CSV File"]);
            } else {
                return redirect()->back()->with(['success' => "Bulk Items uploaded"]);
            }
        }
    }
}
