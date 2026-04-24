<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role');
        $action = $request->query('action');

        $query = AuditLog::with('user')->latest('created_at');

        if ($role) {
            $query->where('role', $role);
        }

        if ($action) {
            $query->where('action', 'like', '%' . $action . '%');
        }

        $logs = $query->paginate(50)->withQueryString();

        return view('Admin.audit-logs.index', [
            'logs' => $logs,
            'role' => $role,
            'action' => $action,
        ]);
    }
}

