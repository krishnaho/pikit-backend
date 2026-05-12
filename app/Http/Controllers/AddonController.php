<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\AddonCategory;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class AddonController extends Controller
{


    public function viewAddonCategories()
    {
        $addonCategories = AddonCategory::get();
        $restaurants = Restaurant::where('is_active', 1)->get();
        return view('admin.addonCategories.addonCategories', [
            'addonCategories' => $addonCategories,
            'restaurants' => $restaurants,
        ]);
    }

    public function addAddonCategory(Request $request)
    {
        $addonCategory = new AddonCategory();
        $addonCategory->name = $request->name;
        $addonCategory->restaurant_id = $request->restaurant_id;
        $addonCategory->type = $request->type;
        $addonCategory->description = $request->description;
        $addonCategory->save();
        return redirect()->back()->with('success', 'Addon Category  Created Successfully');
    }

    public function updateAddonCategory(Request $request)
    {
        $AddonCategory = AddonCategory::where('id', $request->id)->first();
        $AddonCategory->name = $request->name;
        $AddonCategory->restaurant_id = $request->restaurant_id;
        $AddonCategory->type = $request->type;
        $AddonCategory->description = $request->description;
        $AddonCategory->save();
        return redirect()->back()->with('success', 'Addon Category Updated Successfully');
    }

    public function deleteAddonCategory($id)
    {
        $addonCategory = AddonCategory::find($id);

        if ($addonCategory) {
            $addonCategory->delete();
            return redirect()->back()->with(['success' => 'Addon Category Deleted Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }

    public function toggleAddonCategory($id)
    {
        $addonCategory = AddonCategory::find($id);
        if ($addonCategory) {
            $addonCategory->toggleActive();
            $addonCategory->save();
            return redirect()->back()->with(['success' => 'Status Changed Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }



    //     ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //     ---------------------------------------Addon--------------------------------------------------
    //     ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++




    public function viewAddons()
    {
        $addons = Addon::get();
        $addonCategories = AddonCategory::where('is_active', 1)->get();
        return view('admin.addons.addons', [
            'addons' => $addons,
            'addonCategories' => $addonCategories,
        ]);
    }

    public function addAddon(Request $request)
    {
        $addon = new addon();
        $addon->name = $request->name;
        $addon->addon_category_id = $request->addon_category_id;
        $addon->price = $request->price;
        $addon->save();
        return redirect()->back()->with('success', 'Addon Created Successfully');
    }

    public function updateAddon(Request $request)
    {
        $addon = Addon::where('id', $request->id)->first();
        $addon->name = $request->name;
        $addon->addon_category_id = $request->addon_category_id;
        $addon->price = $request->price;
        $addon->save();
        return redirect()->back()->with('success', 'Addon Updated Successfully');
    }

    public function deleteAddon($id)
    {
        $addon = Addon::find($id);
        if ($addon) {
            $addon->delete();
            return redirect()->back()->with(['success' => 'Addon Deleted Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }

    public function toggleAddon($id)
    {
        $addon = Addon::find($id);
        if ($addon) {
            $addon->toggleActive();
            $addon->save();
            return redirect()->back()->with(['success' => 'Status Changed Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }
}
