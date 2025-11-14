<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Project;
use App\Models\Comment;
use App\Models\Student;
use App\Models\Investor;
use App\Policies\ProjectPolicy;
use App\Policies\CommentPolicy;
use App\Policies\StudentPolicy;
use App\Policies\InvestorPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);
        Gate::policy(Student::class, StudentPolicy::class);
        Gate::policy(Investor::class, InvestorPolicy::class);
    }
}
