<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Scheme;
use App\Models\User;
use App\Models\UserScheme;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{

    public function viewUsers()
    {
        $users = User::get();
        $roles = Role::all();
        return view('admin.users.users', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    public function viewAllCustomers()
    {
        $users = User::role('Customer')->get();
        $roles = Role::where('name', 'Customer')->get();
        return view('admin.users.customerUser', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    public function viewAllRestaurantOwners()
    {
        $users = User::role('Restaurant Owner')->get();
        $roles = Role::where('name', 'Restaurant Owner')->get();
        return view('admin.users.restaurantOwner', [
            'users' => $users,
            'roles' => $roles
        ]);
    }
    public function addUser(Request $request)
    {
        $user = User::where('phone', $request->phone)->orWhere('email', $request->email)->first();
        if ($user) {
            return redirect()->back()->with(['warning' => "User Email Or Phone Already Used"]);
        }
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->phone = $request->phone;
        if ($request->password) {
            $user->password = \Hash::make($request->password);
        }
        if ($request->has('role')) {
            $user->assignRole($request->role);
        }
        if ($request->file('image')) {
            $request->validate([
                'images' => '|image|mimes:jpeg,png,jpg|max:200',
            ]);
            $file = $request->file('image');
            $imageName = time() . $file->getClientOriginalName();
            $file->move(public_path('/users/'), $imageName);
            $user->image = 'users/' . $imageName;
        }
        $user->save();

        return redirect()->back()->with('success', 'User Created Successfully');
    }
    public function editUser($id)
    {
        $user = User::where('id', $id)->first();
        $roles = Role::all();
        $orders = Order::where('id', $id)->with('orderstatus')->orderBy('created_at', 'desc')->get();
        return view('admin.users.editUser', [
            'user' => $user,
            'roles' => $roles,
            'orders' => $orders
        ]);
    }
    public function updateUser(Request $request)
    {

        $user = User::where('id', $request->id)->first();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->phone = $request->phone;
        $request->validate([
            'images' => 'image|mimes:jpeg,png,jpg,jfif,pjpeg,pjp,svg,webp|max:50',
        ]);
        if ($request->file('image')) {
            $file = $request->file('image');
            $imageName = time() . $file->getClientOriginalName();
            $file->move(public_path('/users/'), $imageName);
            @unlink(public_path($user->image));
            $user->image = 'users/' . $imageName;
        }
        $user->save();
        return redirect()->back()->with(['success' => 'Details Updated Successfully']);
    }

    public function toggleUser($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->toggleActive();
            $user->save();
            return redirect()->back()->with(['success' => 'Status Changed Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }
    public function updateRole(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        if ($request->password) {
            $user->password = \Hash::make($request->password);
        }
        if ($request->has('role')) {
            $user->roles()->sync($request->role);
        }
        $user->save();
        return redirect()->back()->with(['success' => "Role Updated Successfully"]);
    }

    public function updatePlan(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        $user->active_plan_id = $request->plan_id;
        $user->save();
        return redirect()->back()->with(['success' => "Plan Updated Successfully"]);
    }
    public function updateBankDetails(Request $request)
    {
        $user = User::find($request->id);
        $user->holder_name = $request->holder_name;
        $user->account_number = $request->account_number;
        $user->ifsc_code = $request->ifsc_code;
        $user->bank_name = $request->bank_name;
        $user->save();
        return redirect()->back()->with(['success' => 'User Bank Details Updated Successfully']);
    }

    public function UserWalletTransactions(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        if ($user) {
            if ($request->type == 'DEPOSIT') {
                $user->deposit($request->amount, ['description' => $request->message]);
                return redirect()->back()->with(['success' => "Amount Deposited"]);
            } else {
                $amount = $user->balance;
                if ($request->amount > $amount) {
                    return redirect()->back()->with(['warning' => "You Balance Is Not Enough!"]);
                } else {
                    $user->withdraw($request->amount, ['description' => $request->message]);
                    return redirect()->back()->with(['success' => "Amount Credited"]);
                }
            }
        } else {
            return redirect()->back()->with(['Something Went Wrong!']);
        }
    }
    public function deleteUser($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->delete();
            return redirect()->back()->with(['success' => 'User Deleted Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }
    public function CoinWalletTransactions(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        if ($user) {
            $wallet = $user->getCoinWallets();
            if ($request->type == 'DEPOSIT') {
                $wallet->deposit($request->amount_coin, ['description' => $request->message_coin]);
                $user->total_ko_coin += $request->amount_coin;

                return redirect()->back()->with(['success' => "Amount Deposited"]);
            } else {
                $amount = $user->getCoinWallets()->balance;
                if ($request->amount_coin > $amount) {
                    return redirect()->back()->with(['warning' => "You Balance Is Not Enough!"]);
                } else {
                    $wallet->withdraw($request->amount_coin, ['description' => $request->message_coin]);
                    return redirect()->back()->with(['success' => "Amount Credited"]);
                }
            }
        } else {
            return redirect()->back()->with(['somthing Went Wrong!']);
        }
    }

    public function addMoneyToWallet(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        if ($user) {
            try {
                if ($request->adjustment == 'deposit') {
                    $user->deposit($request->amount, ['description' => $request->message]);
                } else {
                    if ($user->balanceFloat >= $request->amount) {
                        $user->withdraw($request->amount, ['description' => $request->message]);
                    } else {
                        return redirect()->back()->with(['error' => 'Substract amount is less than the user balance amount.']);
                    }
                }
                return redirect()->back()->with(['success' => config('settings.walletName') . ' Updated']);
            } catch (\Illuminate\Database\QueryException $qe) {
                return redirect()->back()->with(['message' => $qe->getMessage()]);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            } catch (\Throwable $th) {
                return redirect()->back()->with(['message' => $th]);
            }
        } else {
            return redirect()->back()->with(['error' => 'User Not found, Something Went Wrong. Try Again']);
        }
    }


    public function impersonate($id)
    {
        $user = User::where('id', $id)->first();
        try {
            if ($user && $user->hasRole('Restaurant Owner')) {
                Auth::user()->impersonate($user);
                return redirect()->route('restaurantOwner.dashboard');
            } else {

                return redirect()->route('home')->with(['message' => 'User not found']);
            }
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['error' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th]);
        }
    }

    public function manageRestaurant($id)
    {
        $authCityIds = Auth::user()->cities->pluck('id')->toArray();
        $user = User::where('id', $id)->with('restaurants')->first();
        $restaurantOwnerRestaurant = $user->restaurants()->get();
        $restaurantOwnerRestaurantIds = $user->restaurants()->pluck('restaurant_id')->toArray();
        if (Auth::user()->hasRole('Super Admin')) {
            $restaurants = Restaurant::where('is_deleted', 0)->get();
        } else {
            $restaurants = Restaurant::where('is_deleted', 0)->whereIn('city_id', $authCityIds)->get();
        }

        // dd($stores);
        return view('admin.users.manageRestaurant', array(
            'restaurantOwnerRestaurant' => $restaurantOwnerRestaurant,
            'user' => $user,
            'restaurants' => $restaurants,
            'restaurantOwnerRestaurantIds' => $restaurantOwnerRestaurantIds,
        ));
    }


    public function updateManageRestaurant(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        try {

            $user->restaurants()->sync($request->restaurants);
            $user->save();
            return redirect()->back()->with('success', 'Store User Updated Successfully');
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['error' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th]);
        }
    }


}
