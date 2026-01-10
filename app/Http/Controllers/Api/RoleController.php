<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        return response()->json([
            'roles' => Role::with('permissions')->get(),
            'permissions' => Permission::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $slug = Str::slug($request->name);
        
        // Check if role with this slug already exists
        if (Role::where('slug', $slug)->exists()) {
            return response()->json([
                'message' => 'A role with this name already exists'
            ], 422);
        }

        $role = Role::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role->load('permissions')
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $slug = Str::slug($request->name);
        
        // Check if another role with this slug already exists (excluding current role)
        if (Role::where('slug', $slug)->where('id', '!=', $role->id)->exists()) {
            return response()->json([
                'message' => 'A role with this name already exists'
            ], 422);
        }

        // Prevent modifying system critical roles if necessary, 
        // but for now we allow editing permissions for flexibility.
        // We might want to prevent changing the slug of core roles.
        
        $role->update([
            'name' => $request->name,
            // 'slug' => Str::slug($request->name), // Usually better not to change slugs of existing roles to break references
            'description' => $request->description
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role->load('permissions')
        ]);
    }

    public function destroy(Role $role)
    {
        if (in_array($role->slug, ['super_admin', 'admin', 'vendor', 'user'])) {
            return response()->json(['message' => 'System roles cannot be deleted'], 403);
        }

        $role->delete();

        return response()->json(['message' => 'Role deleted successfully']);
    }
}
