<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Observability\Metrics;

class MetricsController extends Controller
{
    public function index()
    {
        return response(Metrics::renderPrometheus(), 200, [
            'Content-Type' => 'text/plain; version=0.0.4'
        ]);
    }
}


