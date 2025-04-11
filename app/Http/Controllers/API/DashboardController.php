<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\DashboardService;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $data = $this->dashboardService->getDashboardData();

        return response()->json([
            'status' => 200,
            'message' => "Dashboard Data Fetched Successfully",
            'data' => $data,
        ], 200);
    }
}
