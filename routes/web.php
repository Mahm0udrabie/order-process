<?php

use App\Http\Controllers\OrderProcessController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('order-process', [OrderProcessController::class, 'processOrder']);
