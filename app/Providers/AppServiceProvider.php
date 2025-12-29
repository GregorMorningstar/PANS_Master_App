<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $bindings = [
            \App\Services\Contracts\UserServiceInterface::class => \App\Services\UserService::class,
            \App\Repositories\Contracts\UserRepositoryInterface::class => \App\Repositories\Eloquent\EloquentUserRepository::class,
            \App\Repositories\Contracts\DepartmentsRepositoryInterface::class => \App\Repositories\Eloquent\EloquentDepartmentsRepository::class,
            \App\Services\Contracts\DepartmentsServiceInterface::class => \App\Services\DepartmentsService::class,
            \App\Services\Contracts\FlagServiceInterface::class => \App\Services\FlagService::class,
            \App\Repositories\Contracts\FlagRepositoryInterface::class => \App\Repositories\Eloquent\EloquentFlagRepository::class,
            \App\Services\Contracts\MachinesServiceInterface::class => \App\Services\MachineService::class,
            \App\Repositories\Contracts\MachinesRepositoryInterface::class => \App\Repositories\Eloquent\EloquentMachinesRepository::class,
            \App\Services\Contracts\OperationMachineServiceInterface::class => \App\Services\OperationMachineService::class,
            \App\Repositories\Contracts\OperationMachineRepositoryInterface::class => \App\Repositories\Eloquent\EloquentOperationMachineRepository::class,
            \App\Services\Contracts\MachineFailureServiceInterface::class => \App\Services\MachineFailureService::class,
            \App\Repositories\Contracts\MachineFailureRepositoryInterface::class => \App\Repositories\Eloquent\EloquentMachineFailureRepository::class,
            \App\Services\Contracts\LeavesServiceInterface::class => \App\Services\LeavesService::class,
            \App\Repositories\Contracts\LeavesRepositoryInterface::class => \App\Repositories\Eloquent\EloquentLeavesRepository::class,
        ];

        foreach ($bindings as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
