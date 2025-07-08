<?php

namespace App\Http\Middleware;

use App\Models\StudyMode;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveSemesterIsSet
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $studyModes = StudyMode::all();

        foreach ($studyModes as $studyMode) {
            if (! $studyMode->activeSemester()) {
                return to_route('calendars.index')->withErrors(['no-active-semester' => 'Please make sure to set active semesters for all the study modes before continuing']);
            }
        }
        return $next($request);
    }
}
