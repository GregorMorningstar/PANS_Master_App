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
            $this->app->bind(\App\Services\Contracts\UserServiceInterface::class, \App\Services\UserService::class);
            $this->app->bind(\App\Repositories\Contracts\UserRepositoryInterface::class, \App\Repositories\Eloquent\EloquentUserRepository::class);
            $this->app->bind(\App\Repositories\Contracts\DepartmentsRepositoryInterface::class, \App\Repositories\Eloquent\EloquentDepartmentsRepository::class);
            $this->app->bind(\App\Services\Contracts\DepartmentsServiceInterface::class, \App\Services\DepartmentsService::class);
            $this->app->bind(\App\Services\Contracts\FlagServiceInterface::class, \App\Services\FlagService::class);
            $this->app->bind(\App\Repositories\Contracts\FlagRepositoryInterface::class, \App\Repositories\Eloquent\EloquentFlagRepository::class);
            $this->app->bind(\App\Services\Contracts\MachinesServiceInterface::class, \App\Services\MachineService::class);
            $this->app->bind(\App\Repositories\Contracts\MachinesRepositoryInterface::class, \App\Repositories\Eloquent\EloquentMachinesRepository::class);
            $this->app->bind(\App\Services\Contracts\OperationMachineServiceInterface::class, \App\Services\OperationMachineService::class);
            $this->app->bind(\App\Repositories\Contracts\OperationMachineRepositoryInterface::class, \App\Repositories\Eloquent\EloquentOperationMachineRepository::class);




        }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

//

            }
}
