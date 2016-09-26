<?php

namespace App\Http\Middleware;

use App\Models\School\School;
use Closure;

class SetupMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $school = School::mySchool();

        switch ($school->setup){
            case School::ACADEMIC_YEAR: return redirect('/academic-years');
            case School::ACADEMIC_TERM: return redirect('/academic-terms');
            case School::CLASS_GROUP: return redirect('/class-groups');
            case School::CLASS_LEVEL: return redirect('/class-levels');
            case School::CLASS_ROOM: return redirect('/class-rooms');
            case School::SUBJECT: return redirect('/school-subjects');
//            case School::SUBJECT_CLASS: return redirect('/subject-classrooms');
            case School::ASSESSMENT: return redirect('/assessment-setups');
            case School::ASSESSMENT_DETAIL: return redirect('/assessment-setups/details');
            case School::GRADE: return redirect('/grades');
//            case School::COMPLETED: return redirect('/dashboard');
        }
        return $next($request);
    }
}
