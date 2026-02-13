<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class AdminController extends Controller
{
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name'
        ]);

        $role = $request->role;

        $user->syncRoles([$role]); // replaces old roles

        return response()->json([
            'message' => "Role '{$role}' assigned to {$user->name}"
        ], 200);
    }

    public function givePermission(Request $request, User $user)
    {
        $request->validate([
            'permission' => 'required|string|exists:permissions,name'
        ]);

        $permission = $request->permission;

        $user->givePermissionTo($permission);

        return response()->json([
            'message' => "Permission '{$permission}' granted to {$user->name}"
        ], 200);
    }


    public function removeRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name'
        ]);

        $user->removeRole($request->role);

        return response()->json(['message' => 'Role removed'], 200);
    }



}
