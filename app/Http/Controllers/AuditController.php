<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function index(): View
    {
        $logs = AuditLog::query()
            ->with('user')
            ->orderByDesc('created_at')
            ->get();

        return view('modules.audit.index', ['logs' => $logs]);
    }

    public function show(AuditLog $log): View
    {
        $log->load('user');

        return view('modules.audit.show', ['log' => $log]);
    }
}
