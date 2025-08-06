<?php

namespace App\Providers;

use App\Policies\InstructorPortalPolicy;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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
        Gate::define('manage-course-section-pair', [InstructorPortalPolicy::class, 'canManageCourseSectionPair']);
        Gate::define('view-course-details', [InstructorPortalPolicy::class, 'canViewCourseDetails']);

        JsonResource::withoutWrapping();
        Vite::prefetch(concurrency: 3);
    }
}
