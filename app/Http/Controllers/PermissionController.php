<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//use Spatie\Permission\Models\Permission;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        return Permission::with('group')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions',
            'group_id' => 'nullable|exists:permission_groups,id'
        ]);

        $permission = Permission::create($request->all());

        return response()->json(['permission' => $permission], 201);
    }
}
