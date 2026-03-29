<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class AboutController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'title'    => 'Laravel 12',
            'subtitle' => 'Backend Engine',
        ]);
    }
}
