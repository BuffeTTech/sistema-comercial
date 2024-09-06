<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\SatisfactionQuestion;
use App\Policies\SurveyQuestionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        SatisfactionQuestion::class => SurveyQuestionPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define o super-admin
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Commercial-Admin') ? true : null;
        });
    }
}
