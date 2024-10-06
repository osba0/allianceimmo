<?php

namespace App\Http\Controllers;

use App\Models\PermissionGroup;
use Illuminate\Http\Request;
//use Spatie\Permission\Models\Permission;
use App\Models\Permission;

class PermissionGroupController extends Controller
{
    public function index()
    {
        return PermissionGroup::with('permissions')->get();
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:permission_groups']);
        $group = PermissionGroup::create($request->all());
        return response()->json(['group' => $group], 201);
    }
}
