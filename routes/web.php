<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\AcademicianController;
use App\Http\Controllers\Auth\LoginController as AuthLoginController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\ResearchGrantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\SubstationController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SensorAnalyticsController;
use App\Http\Controllers\LogReportController;




// Public Routes
Route::get('/', function () {
    return view('welcome');
});
Route::get('/datasets/upload', [DatasetController::class, 'showUploadForm'])->name('datasets.upload.form');
Route::post('/datasets/upload', [DatasetController::class, 'handleUpload'])->name('datasets.upload');
Route::resource('academicians', AcademicianController::class);


Route::prefix('reports')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('reports.index');
    
    // Single endpoint for all report actions
    Route::match(['get', 'post'], '/sensor-status', [ReportController::class, 'sensorStatusSummary'])
        ->name('reports.sensor-status');
    Route::get('/analytics/export', [ReportController::class, 'exportSensorAnalytics'])->name('analytics.export');
});
// Route::resource('sensors', SensorController::class)->middleware('auth');
Route::resource('sensors', SensorController::class);

Route::get('/api/switchgears/by-substation/{substation}', function ($substationId) {
    return \App\Models\Switchgear::where('substation_id', $substationId)->get(['id', 'panel']);
});
// Route::get('/api/switchgears/by-substation/{id}', function ($id) {
//     return \App\Models\Switchgear::where('substation_id', $id)->get(['id', 'panel', 'latitude', 'longitude']);
// });


// Route::get('/sensors/manage', [\App\Http\Controllers\SensorController::class, 'man'])->name('sensors.view');
Route::get('/sensors/manage/{sensor}', [SensorController::class, 'show'])->name('sensors.view');

// Route::post('/sensors/update-status/{sensor}', [\App\Http\Controllers\SensorController::class, 'updateStatus'])->name('sensors.updateStatus');
// Route::get('/analytics', [AnalyticsController::class, 'substations'])->name('analytics.substations');
Route::get('/analytics/substations', [AnalyticsController::class, 'substation'])->name('analytics.substations');
Route::get('/analytics/sensors', [SensorAnalyticsController::class, 'index'])->name('analytics.sensors');

// All fixed substation routes
Route::get('/substations/summary', [SubstationController::class, 'summary'])->name('substations.summary');
Route::get('/substations/create', [SubstationController::class, 'create'])->name('substations.create');

// Resource route
Route::resource('substations', SubstationController::class);

// Other dynamic substation route (optional override)
Route::get('/substations/{substation}', [SubstationController::class, 'show'])->name('substation.show');



Route::get('/popup-test', function () {
    return view('partial-dashboard.alert-popup');
});

// Route::controller(LogReportController::class)->prefix('log-reports')->middleware('auth')->group(function () {
//     Route::get('/', 'index')->name('log-reports.index');
//     Route::get('/create', 'create')->name('log-reports.create');
//     Route::post('/', 'store')->name('log-reports.store');
//     Route::get('/{logReport}', 'show')->name('log-reports.show');
//     Route::post('/{logReport}/resolve', 'resolve')->name('log-reports.resolve');
// });

Route::get('/log-reports', [LogReportController::class, 'index'])->name('log-reports.index');
Route::get('/log-reports/create', [LogReportController::class, 'create'])->name('log-reports.create');
Route::post('/log-reports', [LogReportController::class, 'store'])->name('log-reports.store');
Route::get('/log-reports/{logReport}', [LogReportController::class, 'show'])->name('log-reports.show');
Route::post('/log-reports/{logReport}/resolve', [LogReportController::class, 'resolve'])->name('log-reports.resolve');









// Authentication Routes
Auth::routes(); 

// Protected Routes: Accessible only by authenticated users
Route::middleware(['auth'])->group(function () {
    // Dashboard route
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    // Route::get('/dashboard', [DashboardController::class, 'index']);


    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    Route::get('/log-reports', [LogReportController::class, 'index'])->name('log-reports.index');
    Route::get('/log-reports/create', [LogReportController::class, 'create'])->name('log-reports.create');
    Route::post('/log-reports', [LogReportController::class, 'store'])->name('log-reports.store');
    Route::get('/log-reports/{logReport}', [LogReportController::class, 'show'])->name('log-reports.show');
    Route::post('/log-reports/{logReport}/resolve', [LogReportController::class, 'resolve'])->name('log-reports.resolve');
    Route::get('/log-reports/{id}/edit-status', [LogReportController::class, 'editStatus'])->name('log-reports.editStatus');
    Route::put('/log-reports/{id}/update-status', [LogReportController::class, 'updateStatus'])->name('log-reports.updateStatus');


    
    Route::get('/search', function (Request $request) {
        $query = $request->input('query');
        $results = []; // Replace with actual search logic.
        return view('search-results', ['results' => $results, 'query' => $query]);
    })->name('search');
    
    // Alias for backward compatibility with `route('profile')`
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    

    Route::resource('researchgrants', ResearchGrantController::class);
    Route::get('/researchgrants', [ResearchGrantController::class, 'index'])->name('researchgrants.index');

    //Milestone Route
    // Route::resource('researchgrants.milestones', MilestoneController::class);
   
    // Route::get('researchgrants/{researchgrant}/milestones/create', [MilestoneController::class, 'create'])->name('researchgrants.milestones.create');
    // Route::post('/researchgrants/milestones', [MilestoneController::class, 'store'])->name('researchgrants.milestones.store');
    // Route::get('researchgrants/{researchgrant}/milestones', [MilestoneController::class, 'index'])->name('researchgrants.milestones.index');

    // Milestone routes
    Route::get('researchgrants/{researchgrant}/milestones', [MilestoneController::class, 'index'])->name('researchgrants.milestones.index');
    Route::get('researchgrants/{researchgrant}/milestones/create', [MilestoneController::class, 'create'])->name('researchgrants.milestones.create');
    Route::post('researchgrants/{researchgrant}/milestones', [MilestoneController::class, 'store'])->name('researchgrants.milestones.store');
    Route::get('researchgrants/{researchgrant}/milestones/{milestone}/edit', [MilestoneController::class, 'edit'])->name('researchgrants.milestones.edit');
    Route::put('/researchgrants/{researchGrantId}/milestones/{milestoneId}', [MilestoneController::class, 'update'])->name('researchgrants.milestones.update');;
    Route::patch('researchgrants/{researchGrant}/milestones/{milestone}/update-status', [MilestoneController::class, 'updateStatus'])
    ->name('researchgrants.milestones.update-status');

    Route::delete('researchgrants/{researchgrant}/milestones/{milestone}', [MilestoneController::class, 'destroy'])->name('researchgrants.milestones.destroy');
    Route::get('researchgrants/{researchgrant}/milestones/{milestone}', [MilestoneController::class, 'show'])
    ->name('researchgrants.milestones.show');

    Route::resource('researchgrants.milestones', MilestoneController::class)
    ->only(['index', 'show', 'edit', 'update', 'destroy']);

    // Route::resource('academicians', AcademicianController::class);


    // Admin Routes: Controlled by Gate 'admin-only'
    // Route::middleware(['can:admin-only'])->group(function () {
        // Route::resource('academicians', AcademicianController::class);
        
        Route::get('/forms/elements', fn () => view('components.forms.elements'))->name('forms.elements');
    // });

    // // Leader Routes: Controlled by Gate 'leader-only'
    // Route::middleware(['can:leader-only'])->group(function () {
    // // Route::resource('researchgrants.milestones', MilestoneController::class);
    
    // });

    // iRMC Staff Routes: Controlled by Gate 'irmc-staff-only'
    Route::middleware(['can:irmc-staff-only'])->group(function () {
        // Route::get('/researchgrants', [ResearchGrantController::class, 'index'])->name('researchgrants.index');
        Route::get('/ui/alerts', fn () => view('components.ui.alerts'))->name('ui.alerts');
        Route::get('/icons/bootstrap', fn () => view('components.icons.bootstrap'))->name('icons.bootstrap');
    });

    
});
