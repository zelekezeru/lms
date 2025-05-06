<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => fn () => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'profileImg' => $request->user()->profile_img ? Storage::url($request->user()->profile_img) : null,
                    'roles' => cache()->remember(
                        "user_roles_{$request->user()->id}",
                        now()->addMinutes(10),
                        fn () => $request->user()->getRoleNames()
                    ),
                    'permissions' => cache()->remember(
                        "user_permissions_{$request->user()->id}",
                        now()->addMinutes(10),
                        fn () => $request->user()->getAllPermissions()->pluck('name')
                    ),
                ] : null,
            ],
        ];
    }

    protected array $middlewareGroups = [
        'web' => [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            HandleInertiaRequests::class, // ✅ Add Inertia Middleware Here
        ],
    ];
}
