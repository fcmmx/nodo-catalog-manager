<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $metrics = [
            'total' => Product::count(),
            'activos' => Product::where('status', 'activo')->count(),
            'borradores' => Product::where('status', 'borrador')->count(),
            'sin_imagen' => Product::whereNull('main_image')->count(),
            'sin_descripcion' => Product::where(function ($q) {
                $q->whereNull('description')->orWhere('description', '');
            })->count(),
            'colecciones' => Collection::count(),
            'destacados' => Product::where('is_featured', true)->count(),
        ];

        $growth = Product::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as periodo, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->pluck('total', 'periodo');

        $byCollection = Collection::withCount('products')
            ->orderByDesc('products_count')
            ->limit(6)
            ->get(['id', 'name']);

        $recentActivity = Activity::with('causer')->latest()->limit(8)->get();

        $recentProducts = Product::with('collection')->latest()->limit(6)->get();

        return view('dashboard', [
            'metrics' => $metrics,
            'growth' => $growth,
            'byCollection' => $byCollection,
            'recentActivity' => $recentActivity,
            'recentProducts' => $recentProducts,
        ]);
    }
}
