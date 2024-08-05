<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminRequest;
use App\Models\AdminPermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    public function getAdmins()
    {
        $admins =  User::where('role_id', 3)->with('admin_premission')->get();
        return response()->json(['data' => $admins]);
    }

    public function showAdmin($admin_id)
    {
        $admin =  User::where('role_id', 3)->with('admin_premission')->findOrFail($admin_id);
        return response()->json(['data' => $admin]);
    }

    public function storeAdmin(AdminRequest $request)
    {
        $data = $request->all();
        $data['role_id'] = 3;
        $data['email_verified_at'] = now();
        $data['password'] = Hash::make($request->password);
        $admin =  User::create($data);
        $data['admin_id'] = $admin->id;
        AdminPermission::create($data);
        return response()->json(['message' => 'Admin created successfully']);
    }

    public function updateAdmin(AdminRequest $request, $admin_id)
    {
        $data = $request->all();
        $admin =  User::where('role_id', 3)->find($admin_id);
        if (!isset($admin) and $admin->id == 1 and auth()->id() != 1)
            return response()->json(['message' => 'this admin not found'], 404);
        $admin->update($data);

        AdminPermission::where('admin_id', $admin_id)->first()->update($data);

        return response()->json(['message' => 'Admin updated successfully']);
    }

    public function deleteAdmin($admin_id)
    {
        if ($admin_id == 1)
            return response()->json(['message' => 'you can not delete super admin'], 404);

        $admin = User::where('role_id', 3)->findOrFail($admin_id);
        if (!isset($admin) and $admin->id == 1)
            return response()->json(['message' => 'this admin not found'], 404);

        $admin->delete();
        return response()->json(['message' => 'Admin deleted successfully']);
    }

    public function artisanOrder(Request $request)
    {
        $status = Artisan::call($request->order);
        return response()->json([$request['order'] => 'success', 'status' => $status]);
    }
}
