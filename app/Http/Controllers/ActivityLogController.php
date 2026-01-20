<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
 public function index()
{
    // Sekarang kita izinkan Manager (Owner) untuk melihat Log
    if (auth()->user()->role !== 'manager' && auth()->user()->role !== 'admin') {
        abort(403, 'Akses khusus Owner & Admin.');
    }

    $logs = ActivityLog::with('user')->latest()->paginate(50);
    return view('activity_logs.index', compact('logs'));
}
}