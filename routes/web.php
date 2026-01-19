<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DepartamentsController;
use App\Http\Controllers\MachinesController;
use App\Http\Controllers\MachineOperationController;
use App\Http\Controllers\MachineFailuresController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\EmployeeDetailsController;
use App\Http\Controller\UserProfileController;
use App\Http\Controllers\Api\CompanyController;
// Broadcasting authentication routes
Broadcast::routes(['middleware' => ['web', 'auth']]);

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified', 'role:moderator'])
    ->prefix('moderator')
    ->name('moderator.')
    ->group(function () {
        // dashboard: /moderator/dashboard
        Route::get('/dashboard', [ModeratorController::class, 'dashboard'])->name('dashboard');

        // machines: /moderator/machines
        Route::prefix('machines')->name('machines.')->group(function () {
            Route::get('/', [MachinesController::class, 'moderatorIndex'])->name('machines.index');
            Route::get('/add-new', [MachinesController::class, 'create'])->name('machines.create');
            Route::post('/add-new', [MachinesController::class, 'store'])->name('machines.store');
            Route::get('/{id}/edit', [MachinesController::class, 'edit'])->name('machines.edit');
            Route::put('/{id}', [MachinesController::class, 'update'])->name('machines.update');
            Route::delete('/{id}', [MachinesController::class, 'destroy'])->name('machines.destroy');

            // Machine Operations routes z prefiksem
           Route::prefix('operations')->name('operations.')->group(function () {
                Route::get('/', [MachineOperationController::class, 'getAllOperations'])->name('index');
                Route::get('/{machine_id}/add', [MachineOperationController::class, 'createOperation'])->name('create');
                Route::post('/{machine_id}', [MachineOperationController::class, 'storeOperation'])->name('store');
                Route::get('/{operation_id}/show', [MachineOperationController::class, 'showOperation'])->name('show');
                Route::get('/{operation_id}/edit', [MachineOperationController::class, 'editOperation'])->name('edit');
                Route::put('/{operation_id}', [MachineOperationController::class, 'updateOperation'])->name('update');
                Route::delete('/{operation_id}', [MachineOperationController::class, 'destroyOperation'])->name('destroy');
            });
        });

        // users: /moderator/users
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('users.index');
               Route::get('/confirmation-education', [UserController::class, 'confirmationEducation'])->name('users.confirmation-education');
               Route::get('confirmation-work-certificates', [UserController::class, 'confirmationWorkCertificates'])->name('users.confirmation-work-certificates');
            Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
        });

        // leaves: /moderator/leaves
        Route::prefix('leaves')->name('leaves.')->group(function () {
            Route::get('/', [ModeratorController::class, 'getLeavesCalendar'])->name('calendar');
            Route::get('/pending', [ModeratorController::class, 'getPendingLeaves'])->name('pending');
            Route::get('/pending/{userId}', [ModeratorController::class, 'getUserPendingLeaves'])->name('pending.user');
            Route::put('/{leaveId}/approve', [ModeratorController::class, 'approveLeave'])->name('approve');
            Route::put('/{leaveId}/reject', [ModeratorController::class, 'rejectLeave'])->name('reject');
        });

        //departaments: /moderator/departments
        Route::prefix('departments')->group(function () {
            Route::get('/', [DepartamentsController::class, 'moderatorIndex'])->name('departments.index');
            Route::get('/add-new', [DepartamentsController::class, 'create'])->name('departments.create');
            Route::post('/add-new', [DepartamentsController::class, 'store'])->name('departments.store');
            Route::get('/{id}/edit', [DepartamentsController::class, 'edit'])->name('departments.edit');
            Route::put('/{id}', [DepartamentsController::class, 'update'])->name('departments.update');
            Route::delete('/{id}', [DepartamentsController::class, 'destroy'])->name('departments.destroy');
            Route::get('/{id}', [DepartamentsController::class, 'show'])->name('departments.show');
        });
    });



    Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');

    // Machine Failures Reporting
    Route::prefix('machines')->name('machines.')->group(function () {
        Route::get('/report-failure', [MachineFailuresController::class, 'index'])->name('report-failure');

        Route::get('user/', [MachinesController::class, 'getUserMachines'])->name('user.machines');

        Route::prefix('failures')->name('failures.')->group(function () {
            Route::get('/add-new/{machine_id}', [MachineFailuresController::class, 'create'])->name('create');
            Route::post('/', [MachineFailuresController::class, 'store'])->name('store');
                Route::prefix('history')->name('history.')->group(function () {
                    Route::get('/', [MachineFailuresController::class, 'history'])->name('index');
            });
            Route::prefix('edit')->name('edit.')->group(function () {
                Route::get('/{id}', [MachineFailuresController::class, 'edit'])->name('index');
                Route::put('/{id}', [MachineFailuresController::class, 'update'])->name('update');
        });
        Route::delete('/{id}', [MachineFailuresController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('employee')->name('employee.')->group(function () {

        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            Route::get('/', [EmployeeController::class, 'index'])->name('index');
        });
        // Employee calendar routes
        Route::prefix('calendar')->name('calendar.')->group(function () {
            Route::get('/',[CalendarController::class, 'index'])->name('index');
            Route::post('/store',[CalendarController::class, 'store'])->name('store');
            Route::get('/show/{id}',[CalendarController::class, 'show'])->name('show');
            Route::get('/edit/{id}',[CalendarController::class, 'edit'])->name('edit');
            Route::put('/update/{id}',[CalendarController::class, 'update'])->name('update');
            Route::get('/history',[CalendarController::class, 'history'])->name('history');
            Route::get('/details/{id}',[CalendarController::class, 'detailsLeavesById'])->name('details');
        });
         // Employee profile routes
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [EmployeeController::class, 'showEmployeeProfile'])->name('show');

        });
        // Employee address routes
        Route::prefix('adress')->name('address.')->group(function () {
            Route::get('/', [EmployeeController::class, 'showAddress'])->name('show');
            Route::get('/create', [EmployeeController::class, 'createAddress'])->name('create');
            Route::get('/edit', [EmployeeController::class, 'editAddress'])->name('edit');
            Route::post('/', [EmployeeController::class, 'storeAddress'])->name('store');
            Route::put('/update', [EmployeeController::class, 'updateAddress'])->name('update');
        });
        // Employee company routes
        Route::prefix('company')->name('company.')->group(function () {
            Route::get('/', [EmployeeController::class, 'showCompany'])->name('show');
            Route::get('/create', [EmployeeController::class, 'createCompany'])->name('create');
            Route::get('/edit', [EmployeeController::class, 'editCompany'])->name('edit');
            Route::post('/', [EmployeeController::class, 'storeCompany'])->name('store');
            Route::put('/update', [EmployeeController::class, 'updateCompany'])->name('update');
        });
        // Employee education routes
        Route::prefix('education')->name('education.')->group(function () {
            Route::get('/', [EmployeeController::class, 'showEducation'])->name('index');
            Route::get('/create', [EmployeeController::class, 'createEducation'])->name('create');
            Route::get('/{id}/edit', [EmployeeController::class, 'editEducation'])->name('edit');
            Route::post('/', [EmployeeController::class, 'storeEducation'])->name('store');
            Route::put('/{id}', [EmployeeController::class, 'updateEducation'])->name('update');
            Route::delete('/{id}', [EmployeeController::class, 'destroyEducation'])->name('destroy');
            Route::get('/all', [EmployeeController::class, 'showEducationDetails'])->name('show');
        });



    });

});
// Authenticated user dashboard
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});



Route::fallback(function () {
    $user = Auth::user();

    $routeName = $user
        ? match ($user->role) {
            'admin'     => 'admin.dashboard',
            'moderator' => 'moderator.dashboard',
            'employee'  => 'employee.dashboard.index',
            default     => 'dashboard',
        }
        : 'home';

    $targetUrl = route($routeName);

    return response()->view('errors.404-redirect', [
        'targetUrl' => $targetUrl,
        'seconds'   => 5,
        'role'      => $user?->role,
    ], 404);
});
// API routes
Route::prefix('api')->middleware(['web', 'auth'])->group(function () {
    Route::get('/company/nip-lookup/{nip}', [App\Http\Controllers\Api\CompanyController::class, 'nipLookup']);
    Route::get('/schools/search', [App\Http\Controllers\Api\SchoolController::class, 'searchSchools']);
});

// Education routes
Route::middleware(['auth', 'verified'])
    ->prefix('education')
    ->name('education.')
    ->group(function () {
        Route::get('/', [App\Http\Controllers\EducationController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\EducationController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\EducationController::class, 'store'])->name('store');
        Route::get('/{certificate}', [App\Http\Controllers\EducationController::class, 'show'])->name('show');
        Route::get('/{certificate}/edit', [App\Http\Controllers\EducationController::class, 'edit'])->name('edit');
        Route::put('/{certificate}', [App\Http\Controllers\EducationController::class, 'update'])->name('update');
        Route::delete('/{certificate}', [App\Http\Controllers\EducationController::class, 'destroy'])->name('destroy');
    });

require __DIR__.'/settings.php';
