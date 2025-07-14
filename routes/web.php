<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DatasetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Main project routes (protected)
Route::middleware(['auth', 'verified'])->group(function () {
    // Datasets
    Route::get('/datasets', [DatasetController::class, 'index'])->name('datasets.index');
    Route::get('/datasets/create', [DatasetController::class, 'create'])->name('datasets.create');
    Route::post('/datasets', [DatasetController::class, 'store'])->name('datasets.store');
    Route::get('/datasets/{dataset}', [DatasetController::class, 'show'])->name('datasets.show');
    Route::post('/datasets/{dataset}/import', [DatasetController::class, 'import'])->name('datasets.import');
    Route::delete('/datasets/{dataset}', [DatasetController::class, 'destroy'])->name('datasets.destroy');
    Route::get('/datasets/{dataset}/download', [DatasetController::class, 'download'])->name('datasets.download');
    Route::post('/datasets/preview', [DatasetController::class, 'preview'])->name('datasets.preview');

    // Patients
    Route::get('/patients', [PatientController::class, 'index'])->name('patients');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/patient-analytics', [AnalyticsController::class, 'getPatientAnalytics'])->name('analytics.patient');

    // Reports (placeholder)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard route
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

require __DIR__.'/auth.php';
