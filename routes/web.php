<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\PentestingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Home
Route::get('/', [PentestingController::class, 'showPentesting'])->name('pentesting');

//===Scans===
//nmap
Route::post('/run-nmap', [ScanController::class, 'runNmap'])->name('run.nmap');
Route::get('/nmap', [ScanController::class, 'showNmap'])->name('nmap');

//gobuster
Route::post('/run-feroxbuster', [ScanController::class, 'runFeroxbuster'])->name('run.feroxbuster');
Route::get('/feroxbuster', [ScanController::class, 'showFeroxbuster'])->name('feroxbuster');

