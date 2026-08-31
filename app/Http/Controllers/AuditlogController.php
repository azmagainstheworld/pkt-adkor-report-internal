<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->input('range', '7-hari');
        $userId = $request->input('user');
        $moduleKey = $request->input('module');

        $query = AuditLog::with('user')->orderByDesc('created_at');

        $now = Carbon::now();
        match ($range) {
            'hari-ini' => $query->whereDate('created_at', $now->toDateString()),
            '30-hari' => $query->where('created_at', '>=', $now->copy()->subDays(30)),
            default => $query->where('created_at', '>=', $now->copy()->subDays(7)), // '7-hari' & fallback
        };

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($moduleKey) {
            $query->where('module_key', $moduleKey);
        }

        $logs = $query->paginate(15)->withQueryString();

        // Sumber opsi filter: hanya pengguna & modul yang benar-benar pernah tercatat
        $users = User::orderBy('name')->get(['id', 'name']);
        $modules = AuditLog::whereNotNull('module_key')
            ->distinct()
            ->orderBy('module_key')
            ->pluck('module_key');

        return view('log-audit', [
            'logs' => $logs,
            'users' => $users,
            'modules' => $modules,
            'selectedRange' => $range,
            'selectedUser' => $userId,
            'selectedModule' => $moduleKey,
        ]);
    }
}
