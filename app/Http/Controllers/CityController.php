<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function viewCities()
    {
        $cities = City::get();
        $users = User::all();
        return view('admin.cities.cities', [
            'cities' => $cities,
            'users' => $users
        ]);
    }
    public function addCity(Request $request)
    {
        $city = new City();
        $city->name = $request->name;
        $city->latitude = $request->latitude;
        $city->longitude = $request->longitude;
        $city->radius = $request->radius;
        $city->delivery_charge_type = $request->delivery_charge_type;
        $city->delivery_charge = $request->delivery_charge;
        $city->base_delivery_charge = $request->base_delivery_charge;
        $city->base_delivery_distance = $request->base_delivery_distance;
        $city->extra_delivery_charge = $request->extra_delivery_charge;
        $city->extra_delivery_distance = $request->extra_delivery_distance;
        $city->save();
        return redirect()->back()->with('success', 'City Created Successfully');
    }

    public function updateCity(Request $request)
    {
        $city = City::where('id', $request->id)->first();
        $city->name = $request->name;
        $city->latitude = $request->latitude;
        $city->longitude = $request->longitude;
        $city->radius = $request->radius;
        $city->delivery_charge_type = $request->delivery_charge_type;
        $city->delivery_charge = $request->delivery_charge;
        $city->base_delivery_charge = $request->base_delivery_charge;
        $city->base_delivery_distance = $request->base_delivery_distance;
        $city->extra_delivery_charge = $request->extra_delivery_charge;
        $city->extra_delivery_distance = $request->extra_delivery_distance;
        $city->save();
        return redirect()->back()->with('success', 'City Updated Successfully');
    }

    public function deleteCity($id)
    {
        $city = City::find($id);

        if ($city) {
            $city->delete();
            return redirect()->back()->with(['success' => 'City Deleted Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }

    public function toggleCity($id)
    {
        $city = City::find($id);
        if ($city) {
            $city->toggleActive();
            $city->save();
            return redirect()->back()->with(['success' => 'Status Changeed Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }
    public function toggleCitySurge($id)
    {
        $city = City::find($id);
        if ($city) {
            $city->toggleSurgeActive();
            $city->save();
            return redirect()->back()->with(['success' => 'Status Changeed Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }

    public function updateCityUser(Request $request)
    {
        try {
            $city = City::where('id', $request->id)->first();
            $city->users()->sync($request->city_users);
            $city->save();
            return redirect()->back()->with('success', 'City users updated successfully');
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['error' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th]);
        }
    }
}
