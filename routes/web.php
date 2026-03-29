<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['success' => true, 'message' => 'RailManager API v2.0']);
});
