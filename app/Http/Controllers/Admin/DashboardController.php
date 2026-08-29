<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Support\DashboardMetrics;

class DashboardController extends Controller
{
    public function __invoke(DashboardMetrics $metrics)
    {
        return view('admin.dashboard', [
            'tiles'            => $metrics->tiles(),
            'pipeline'         => $metrics->pipeline(),
            'enquiriesByWeek'  => $metrics->enquiriesByWeek(),
            'topListings'      => $metrics->enquiriesByListing(),
            'valueByArea'      => $metrics->valueByArea(),
            'recentEnquiries'  => $metrics->recentEnquiries(),
            'latestProperties' => Property::with('images')->latest()->take(4)->get(),
        ]);
    }
}
