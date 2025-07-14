<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\DecisionSupportController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Patient Routes
    Route::apiResource('patients', PatientController::class);
    Route::get('patients/{patient}/medical-history', [PatientController::class, 'medicalHistory']);

    // Analytics Routes
    Route::prefix('analytics')->group(function () {
        Route::get('/dashboard', [AnalyticsController::class, 'dashboard']);
        Route::get('/patient-analytics', [AnalyticsController::class, 'patientAnalytics']);
    });

    // Medical Records Routes
    Route::apiResource('medical-records', MedicalRecordController::class);
    Route::get('medical-records/statistics', [MedicalRecordController::class, 'statistics']);

    // Data Warehouse Routes
    Route::prefix('warehouse')->group(function () {
        Route::get('/metrics', [AnalyticsController::class, 'getWarehouseMetrics']);
        Route::get('/trends', [AnalyticsController::class, 'getTrendAnalysis']);
        Route::get('/predictions', [AnalyticsController::class, 'getPredictiveAnalytics']);
    });

    // Decision Support Routes
    Route::prefix('decision-support')->group(function () {
        // Clinical Decision Support
        Route::post('/treatment-recommendations', [DecisionSupportController::class, 'getTreatmentRecommendations']);

        // Operational Decision Support
        Route::post('/optimize-resources', [DecisionSupportController::class, 'optimizeResources']);
        Route::post('/staffing-recommendations', [DecisionSupportController::class, 'getStaffingRecommendations']);

        // Strategic Decision Support
        Route::get('/quality-analysis', [DecisionSupportController::class, 'analyzeQuality']);
        Route::get('/cost-analysis', [DecisionSupportController::class, 'analyzeCosts']);
    });

    // Report Routes
    Route::get('/reports/generate', [ReportController::class, 'generateReport']);
});
