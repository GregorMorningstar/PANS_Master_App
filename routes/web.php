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
            Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
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
            // DELETE ('/machines/failures/{id}')`
            Route::delete('/{id}', [MachineFailuresController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('employee')->name('employee.')->group(function () {

        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            Route::get('/', [EmployeeController::class, 'index'])->name('index');
        });
        Route::prefix('calendar')->name('calendar.')->group(function () {
            Route::get('/',[CalendarController::class, 'index'])->name('index');
            Route::post('/store',[CalendarController::class, 'store'])->name('store');
            Route::get('/show/{id}',[CalendarController::class, 'show'])->name('show');
            Route::get('/history',[CalendarController::class, 'history'])->name('history');
        });
               Route::get('/employee-details/{employeeId}', [EmployeeController::class, 'showDetails'])->name('details');

    });

});

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

require __DIR__.'/settings.php';
