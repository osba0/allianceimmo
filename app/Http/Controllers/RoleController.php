<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
//use Spatie\Permission\Models\Permission;
use App\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        return Role::with('permissions')->get();
    }

    public function getRoles()
    {
        return Role::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles'
        ]);

        $role = Role::create(['name' => $request->name]);

        return response()->json(['role' => $role], 201);
    }

    public function assignPermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'required|array'
        ]);

        $permissions = Permission::whereIn('id', $request->permissions)->get();
        $role->syncPermissions($permissions);

        return response()->json(['role' => $role->load('permissions')]);
    }

     /**
     * Assign a permission to a role.
     */
    public function assignPermission(Request $request, Role $role)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $permission = Permission::find($request->permission_id);

        if ($permission) {
            $role->givePermissionTo($permission);
            return response()->json(['message' => 'Permission assigned successfully'], 200);
        }

        return response()->json(['message' => 'Permission not found'], 404);
    }

    /**
     * Remove a permission from a role.
     */
    public function removePermission(Role $role, Permission $permission)
    {
        if ($role->hasPermissionTo($permission)) {
            $role->revokePermissionTo($permission);
            return response()->json(['message' => 'Permission removed successfully'], 200);
        }

        return response()->json(['message' => 'Permission not found'], 404);
    }

    /**
     * Get permissions of a specific role.
     *
     * @param  Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPermissions(Role $role)
    {
        // Eager load the permissions relationship
        $role->load('permissions');

        return response()->json($role->permissions);
    }

}
