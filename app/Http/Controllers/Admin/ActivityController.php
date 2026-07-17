<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $activities = Activity::query()
            ->with('causer')
            ->when($request->log_name, fn ($q) => $q->where('log_name', $request->log_name))
            ->when($request->event, fn ($q) => $q->where('event', $request->event))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $logNames = Activity::query()->distinct()->pluck('log_name');

        return view('admin.activity.index', ['activities' => $activities, 'logNames' => $logNames]);
    }
}
