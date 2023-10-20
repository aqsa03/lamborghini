<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\Response;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('update-user', function (User $auth_user, User $user) {
            if($user->is_root() and !$auth_user->is_root()){
                return Response::deny('You must be a root user.');
            }
            return Response::allow();
        });

        Gate::define('destroy-user', function (User $auth_user, User $user) {
            if($user->is_root() and !$auth_user->is_root()){
                return Response::deny('You must be a root user.');
            }
            if($user->id == $auth_user->id){
                return Response::deny('You cannot delete your user.');
            }
            return Response::allow();
        });
    }
}
