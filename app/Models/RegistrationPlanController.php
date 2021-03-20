<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\RegistrationCourse;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudyYear;
use App\Models\StudyYearSemester;
use Illuminate\Http\Request;
use Auth;
use Validator;
use JWTFactory;
use JWTAuth;
use JWTAuthException;
use App\Models\RegistrationPlan;
use App\Models\Course;
use App\Models\StudentOpenedCourse;

class RegistrationPlanController extends Controller
{
    public $current_study_year_id = 20 ;
    public $current_semester_id = 2 ;

    public function studyYearSemesterId(){

        $study_year_semester = StudyYearSemester::where('study_year_id',$this->current_study_year_id)
            ->where('semester_id',$this->current_semester_id)->first();
        return $study_year_semester->id ;

    }
    public function index()
    {

        $student = Student::find(3466); //authenticated user


        $student_finance_hours = $student->studentFinanceAllowedHours($this->current_study_year_id,$this->current_semester_id);
        $student_academic_hours = $student->studentAcademicAllowedHours($this->current_study_year_id,$this->current_semester_id);

        $registration_plan = RegistrationPlan::where('faculty_id',$student->faculty_id)
             ->where('department_id',$student->department_id)
             ->where('study_year_semester_id',$this->studyYearSemesterId())->first();


        $registration_plan_courses =  $registration_plan->registrationCourses;

         $student_study_plan = $student->StudentStudyPlan();

        $student_opened_courses = $student->studentOpenedCourses;

        $stud_opened_courses_ids = $student_opened_courses ? $student_opened_courses->pluck('course_id') : [];
        $reg_course = [];
        $registration_course_arr = [];
        if (count($stud_opened_courses_ids) !=0 ){

             $student_registration_courses = $registration_plan_courses->whereIn('course_id',$stud_opened_courses_ids) ;


             foreach ($student_registration_courses as  $registration_course ){
                 if ( (!$registration_course->groups_count == 0 || !$registration_course->categories_count == 0 )){
                     $course_credit_hours =  $student_study_plan->courseDetails($registration_course->course_id)->credit_hours;

                     $course_status = $student_opened_courses->where('course_id',$registration_course->course_id)->first()->course_status;

                     $chosen = $student_opened_courses->where('course_id',$registration_course->course_id)->first()->isChosenForRegistration();

                     $course = [
                         'course_id' => $registration_course->course_id,
                         'course_code' => $registration_course->course->code ,
                         'course_name' => $registration_course->course->name ,
                         'course_credit_hours' => $course_credit_hours ,
                         'course_status' => $course_status ,
                         'chosen' => $chosen ]  ;

                     $groups =  $registration_course->courseGroups->map(function ($group){

                         $lectures = $group->lectures->map(function ($lecture){
                             return [
                                 'day' => $lecture->day,
                                 'start_time' => $lecture->start_time,
                                 'end_time' => $lecture->end_time,
                                 'place' => $lecture->place,

                             ];
                         });
                         return ['id' => $group->id , 'name' => $group->name , 'capacity' => $group->capacity ,'lectures' => $lectures];
                     });
                     $categories = $registration_course->courseCategories->map(function ($category){
                         $lectures = $category->lectures->map(function ($lecture){
                             return [
                                 'day' => $lecture->day,
                                 'start_time' => $lecture->start_time,
                                 'end_time' => $lecture->end_time,
                                 'place' => $lecture->place,

                             ];
                         });
                         return [
                             'id' => $category->id ,  'name' => $category->name , 'capacity' => $category->capacity ,'lectures' => $lectures];
                     });
                     $reg_course = $course;
                     $reg_course['groups_count'] = $registration_course->groups_count;
                     $reg_course['categories_count'] = $registration_course->categories_count;
                     $reg_course['groups'] = $groups;
                     $reg_course['categories'] = $categories;

                     array_push($registration_course_arr,$reg_course);

             }
             }

             return [
                 'finance_allowed_hours' => $student_finance_hours,
                 'academic_allowed_hours' => $student_academic_hours ,
                 'registration_courses' => $registration_course_arr,
                 ];
        }
        else {
                return [];
            }




    }
}


