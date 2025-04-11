<?php

namespace App\Services;

use App\Models\Hero;
use App\Models\Post;
use App\Models\Series;

/**
 * Class DashboardService.
 */
class DashboardService
{
    public function getDashboardData()
    {
        return [
            'posts' => Post::count(),
            'heroes' => Hero::count(),
            'series' => Series::count(),
        ];
    }
}
