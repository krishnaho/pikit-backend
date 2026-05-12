<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemGroup;
use App\Models\Restaurant;
use App\Models\RestaurantCategory;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\imports\itemImport;

class ItemController extends Controller
{
    public function viewItemCategories()
    {
        $itemCategories = ItemCategory::get();
        $restaurants = Restaurant::where('is_active', 1)->get();
        return view('admin.itemCategories.itemCategories', [
            'itemCategories' => $itemCategories,
            'restaurants' => $restaurants,
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
        $itemCategory->restaurant_id = $request->restaurant_id;
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
        $itemCategory->restaurant_id = $request->restaurant_id;
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

    //     ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //     ---------------------------------------Item Group---------------------------------------------
    //     ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++



    public function viewItemGroups()
    {
        $itemGroups = itemGroup::get();
        $restaurantCategories = RestaurantCategory::where('is_active', 1)->get();
        return view('admin.itemGroups.itemGroups', [
            'itemGroups' => $itemGroups,
            'restaurantCategories' => $restaurantCategories,
        ]);
    }

    public function addItemGroup(Request $request)
    {
        $ItemGroup = new ItemGroup();
        $ItemGroup->name = $request->name;
        $request->validate([
            'images' => 'image|mimes:jpeg,jpg,max:50',
        ]);
        if ($request->file('image')) {
            $file = $request->file('image');
            $imageName = time() . $file->getClientOriginalName();
            $file->move(public_path('/item-group/'), $imageName);
            $ItemGroup->image = 'item-group/' . $imageName;
        }
        if ($request->file('background_image')) {
            $file = $request->file('background_image');
            $imageName = time() . $file->getClientOriginalName();
            $file->move(public_path('/item-group/'), $imageName);
            $ItemGroup->background_image = 'item-group/' . $imageName;
        }
        $ItemGroup->restaurant_category_id = $request->restaurant_category_id;
        $ItemGroup->save();
        return redirect()->back()->with('success', 'Item Group  Created Successfully');
    }

    public function updateItemGroup(Request $request)
    {
        $ItemGroup = ItemGroup::where('id', $request->id)->first();
        $ItemGroup->name = $request->name;
        if ($request->file('image')) {
            $file = $request->file('image');
            $imageName = time() . $file->getClientOriginalName();
            $file->move(public_path('/item-group/'), $imageName);
            @unlink(public_path($ItemGroup->image));
            $ItemGroup->image = 'item-group/' . $imageName;
        }
        if ($request->file('background_image')) {
            $file = $request->file('background_image');
            $imageName = time() . $file->getClientOriginalName();
            $file->move(public_path('/item-group/'), $imageName);
            @unlink(public_path($ItemGroup->image));
            $ItemGroup->background_image = 'item-group/' . $imageName;
        }
        $ItemGroup->restaurant_category_id = $request->restaurant_category_id;
        $ItemGroup->save();
        return redirect()->back()->with('success', 'Item Group Updated Successfully');
    }

    public function deleteItemGroup($id)
    {
        $ItemGroup = ItemGroup::find($id);

        if ($ItemGroup) {
            $ItemGroup->delete();
            return redirect()->back()->with(['success' => 'Item Group Deleted Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }

    public function toggleItemGroup($id)
    {
        $ItemGroup = ItemGroup::find($id);
        if ($ItemGroup) {
            $ItemGroup->toggleActive();
            $ItemGroup->save();
            return redirect()->back()->with(['success' => 'Status Changed Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }


    //     ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //     ------------------------------------------Items-----------------------------------------------
    //     ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++



    public function viewItems()
    {
        $items = item::get();
        $restaurants = Restaurant::where('is_active', 1)->get();
        $itemCategories = ItemCategory::where('is_active', 1)->get();
        $itemGroups = ItemGroup::get();

        return view('admin.items.items', [
            'items' => $items,
            'restaurants' => $restaurants,
            'itemCategories' => $itemCategories,
            'itemGroups' => $itemGroups
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
        $item->restaurant_id = $request->restaurant_id;
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
        $item->itemGroups()->sync($request->item_groups_id);

        return redirect()->back()->with('success', 'Item Category  Created Successfully');
    }

    public function editItem($id)
    {
        // Find the item by ID
        $item = Item::find($id);

        // Get active restaurants
        $restaurants = Restaurant::where('is_active', 1)->get();

        // Get item categories associated with the item's restaurant
        $itemCategories = ItemCategory::where('is_deleted', 0)
            ->where('restaurant_id', $item->restaurant_id)
            ->get();

        // Handle null case for restaurant and itemCategories
        if ($item->restaurant) {
            $storeItemGroupIds = $item->restaurant->itemCategories->pluck('id')->toArray();
        } else {
            $storeItemGroupIds = []; // Handle null case by assigning an empty array
        }

        // Retrieve all item groups (you can apply any filtering here if needed)
        $itemGroups = ItemGroup::get();

        // Retrieve item group IDs for the item
        $itemGroupItems = $item->itemGroups()->pluck('item_group_id')->toArray();

        // Pass data to the view
        return view('admin.items.itemEdit', [
            'item' => $item,
            'restaurants' => $restaurants,
            'itemCategories' => $itemCategories,
            'itemGroups' => $itemGroups,
            'itemGroupItems' => $itemGroupItems,
        ]);
    }


    public function updateItem(Request $request)
    {
        // dd($request);
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
        $item->restaurant_id = $request->restaurant_id;
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
        $item->itemGroups()->sync($request->item_groups_id);

        return redirect()->back()->with('success', 'Item Category Updated Successfully');
    }

    public function deleteItem($id)
    {
        $item = Item::find($id);

        // dd($item);

        if ($item) {
            $item->delete();
            return redirect()->back()->with(['success' => 'Item Category Deleted Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }

    public function selectDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $item = Item::find($id);
            $item->delete();
        }
        return  response()->json([
            'success' => true,
            'message' => count($request->ids) . ' Items Deleted Successfully',
        ]);
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
        $item = Item::with('addoncategories', 'restaurant.addoncategories')->find($id);
        $selectedCategories = $item->addoncategories->pluck('id')->toArray();
        $addonCategories = $item->restaurant->addoncategories;
        return view('admin.items.itemaddoncategories', array(
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
        $itemCategories = ItemCategory::where('is_deleted', 0)->where('restaurant_id', $request->restaurant_id)->get();
        return response()->json($itemCategories);
    }
    public function getRestaurantItemCategory(Request $request)
    {
        if ($request->restaurant_id) {
            $itemCategories = ItemCategory::where('is_deleted', 0)->where('restaurant_id', $request->restaurant_id)->get();
            $restaurant = Restaurant::where('is_deleted', 0)->where('is_active', 1)->where('id', $request->restaurant_id)->first();
            $response = ([
                'itemCategories' => $itemCategories,
                'restaurant' => $restaurant
            ]);

            return response()->json($response);
        } else {
            return response('no data');
        }
    }
}
