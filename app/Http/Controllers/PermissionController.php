<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PermissionHead;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{

    public function givePermissions()
    {
        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            $role = Role::findByName('Super Admin');
            $role->givePermissionTo($permission);
        }
        $response = ['success' => true, 'data' => 'Permissions Added Successfully'];
        return response()->json($response);
    }

    public function viewPermission()
    {
        $permissionHeads = PermissionHead::get();
        $permissions = Permission::with('permission_head')->get();
        $roles = Role::where('name', '!=', 'Customer')->with('permissions')->with('users')->orderBy('id', 'ASC')->get();
        return view('admin.permission.permission', array(
            'roles' => $roles,
            'permissions' => $permissions,
            'permissionHeads' => $permissionHeads,
        ));
    }

    public function createNewRole(Request $request)
    {
        $role = Role::create($request->except(['permission', '_token']));
        $permission = $request->input('permission') ? $request->input('permission') : [];
        $role->givePermissionTo($permission);
        return redirect()->back()->with(['success' => 'Operation Successful']);
    }

    public function editRole($id)
    {
        $role = Role::where('id', $id)->with('permissions')->firstOrFail();
        $permissions = Permission::with('permission_head')->get();
        $permissionHeads = PermissionHead::get();
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();
        return view('admin.permission.editRole', [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissionIds' => $rolePermissionIds,
            'permissionHeads' => $permissionHeads,
        ]);
    }

    public function updateRole(Request $request, $id)
    {
        $role = Role::where('id', $id)->firstOrFail();
        if ($role) {
            $permissions = $request->input('permission') ? $request->input('permission') : [];
            $role->syncPermissions($permissions);
            return redirect()->back()->with(['success' => 'Permission Updated Successfully']);
        } else {
            return redirect()->back()->with(['success' => 'No such permission']);
        }
    }

    public function deleteRole($id)
    {
        $role = Role::find($id);
        if ($role) {
            $role->delete();
            return redirect()->back()->with(['success' => 'Role Deleted Successfully']);;
        } else {
            return redirect()->back()->with(['success' => 'No such Role']);
        }
    }

    public function createNewPermission(Request $request)
    {
        $permission = new Permission();
        $permission->name = $request->name;
        $permission->readable_name = $request->readable_name;
        $permission->permission_head_id = $request->permission_head_id;
        $permission->save();
        $role = Role::findByName('Super Admin');
        $role->givePermissionTo($permission);
        return redirect()->back()->with(['success' => 'Permission Added Successfully']);
    }
    public function editPermission($id)
    {
        $permission = Permission::where('id', $id)->with('permission_head')->first();
        $permissionHeads = PermissionHead::get();
        return view('admin.permission.editPermission', [
            'permission' => $permission,
            'permissionHeads' => $permissionHeads,

        ]);
    }
    public function updatePermission(Request $request)
    {
        $permission = Permission::find($request->id);
        if ($permission) {
            $permission->name = $request->name;
            $permission->readable_name = $request->readable_name;
            $permission->permission_head_id = $request->permission_head_id;
            $permission->save();
            return redirect()->back()->with(['success' => 'Permission Updated Successfully']);
        } else {
            return redirect()->back()->with(['success' => 'No such permission']);
        }
    }
    public function deletePermission($id)
    {
        $permission = Permission::find($id);
        if ($permission) {
            $permission->delete();
            return redirect()->back()->with(['success' => 'Permission Deleted Successfully']);;
        } else {
            return redirect()->back()->with(['success' => 'No such permission']);
        }
    }
}
