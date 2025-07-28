<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Calculate statistics for the dashboard
        $stats = [
            'total_users' => User::count(),
            'total_forums' => 0, // TODO: Add Forum model count when available
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
