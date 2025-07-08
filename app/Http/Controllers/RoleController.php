<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    // GET /roles - Ambil semua role
    public function index()
    {
        return response()->json([
            'status_code' => 200,
            'message' => 'All roles retrieved successfully',
            'data' => Role::all()
        ]);
    }

    // POST /roles - Tambah role baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $role = Role::create([
            'name' => $request->name
        ]);

        return response()->json([
            'status_code' => 201,
            'message' => 'Role created successfully',
            'data' => $role
        ], 201);
    }

    // GET /roles/{id} - Ambil 1 role
    public function show(Role $role)
    {
        return response()->json([
            'status_code' => 200,
            'message' => 'Role found',
            'data' => $role
        ]);
    }

    // PUT /roles/{id} - Update role
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name,' . $role->id . '|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $role->update([
            'name' => $request->name
        ]);

        return response()->json([
            'status_code' => 200,
            'message' => 'Role updated successfully',
            'data' => $role
        ]);
    }

    // DELETE /roles/{id} - Hapus role
    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json([
            'status_code' => 200,
            'message' => 'Role deleted successfully'
        ]);
    }
}
