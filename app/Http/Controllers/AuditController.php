<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = $request->integer('per_page', 25);

        $logs = AuditLog::query()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('modules.audit.index', ['logs' => $logs]);
    }

    public function show(AuditLog $log): View
    {
        $log->load('user');

        return view('modules.audit.show', ['log' => $log]);
    }
}
