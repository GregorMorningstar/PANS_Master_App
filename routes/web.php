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
        Route::prefix('machines')->group(function () {
            Route::get('/', [MachinesController::class, 'moderatorIndex'])->name('machines.index');
            Route::get('/add-new', [MachinesController::class, 'create'])->name('machines.create');
            Route::post('/add-new', [MachinesController::class, 'store'])->name('machines.store');
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
        });
    });

Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
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
            'employee'  => 'employee.dashboard',
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
