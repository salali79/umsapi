<?php

namespace App\Models;
use Carbon\Carbon;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Astrotomic\Translatable\Translatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Student extends Authenticatable implements JWTSubject
{
    use Translatable ;
    use Notifiable;

    protected $with = ['translations'];

    protected $translatedAttributes =
     ['first_name','middle_name',
     'last_name','mother_name',
     'birthplace','gender',
     'nationality','specific_features',
     'civil_record','writing_hand','amaneh'];

    protected $guarded  =[];

    protected $appends = ['image_path','image_thumb_path','full_name','registration_status'];

	public function getRegistrationStatusAttribute()
    {
        $registration_status = 0;
        $student_registered_course = $this->studentRegisteredCourses;
        if($student_registered_course != null)
        {
            if($student_registered_course->where('status','=',1)->first() != null)
                $registration_status = 1;
            else
                $registration_status = 0 ;
        }
        return $registration_status ;
    }
    public function getFullNameAttribute(){
        return $this->first_name .' '.$this->middle_name .' '.$this->last_name ;
    }
    public function getImagePathAttribute()
    {
        return $this->image ? asset('uploads/'.$this->table.'/'.$this->image): null;
    }
    public function getImageThumbPathAttribute()
    {
        return $this->image ? asset('uploads/'.$this->table.'/thumbs/'.$this->image): null;
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function contact()
    {
        return $this->hasOne(StudentContact::class);
    }
    public function emergency()
    {
        return $this->hasOne(StudentEmergency::class);
    }
    public function medicals()
    {
        return $this->hasMany(StudentMedical::class);
    }
    public function folderType()
    {
        return $this->belongsTo(FolderType::class);
    }
    // relationship to get all files required in student folder depending on student folder type
    public function studentFolderFiles()
    {
        $folder = FolderType::find($this->folder_type_id)->papers;
        return $folder;
    }
    public function folderName()
    {
        $folder = FolderType::find($this->folder_type_id);
        return $folder;
    }
    public function studentFiles()
    {
        return $this->hasMany(StudentFile::class);
    }
    public function deposites()
    {
        return $this->hasMany(StudentDepositRequest::class)->with('bank');
    }
    public function studentRegisterWay(){
        return $this->belongsTo(RegisterWay::class,'register_way_id');
     }
     public function registerParams(){
        return $this->hasMany(StudentRegisterParam::class);
     }
     public function getBirthday(){
        /*birthday->format('Y/m/d')*/
         $date = new Carbon ($this->birthday);
         return $date->day.'/'.$date->month.'/'.$date->year;
     }
     public function semesterTranscript(){
        $s_trans = SemesterTranscript::query()
            ->student($this->id)
            ->orderBy('created_at','desc')
            ->first();
        return $s_trans ;
     }
    public function studentSemesterTranscript(){
        return $this->hasMany(StudentSemesterTranscript::class);
    }
    public function finalTranscript(){
        return $this->hasOne(StudentFinalTranscript::class);
    }
    public function finance(){
        return $this->hasOne(FinanceAccount::class);
     }
    public function financeDetails(){
        return $this->hasManyThrough(FinanceAllowedHours::class,FinanceAccount::class);
    }
    public static function getStudentId($academic_number){
        $student = Student::where('academic_number','=',$academic_number)->first();
        if($student){
            return $student->id ;
        }
    }
    public static function getStudentFinance($student_id){
        $student = Student::find($student_id);
        $finance = $student->finance;
        return $finance ;

    }
    public static function getStudentFaculty($student_id){
        $student = Student::find($student_id);
        $faculty = $student->faculty;
        return $faculty ;

    }
    public static function getStudentDepartment($student_id){
        $student = Student::find($student_id);
        $department = $student->department;
        return $department ;

    }
    public static function StudentStudyYear($student_id){
        $student = Student::find($student_id);
        $studyYear = StudyYear::
            where('beginning','<=', date("d-m-Y", strtotime(($student->registration_date))))
            ->where('end','>=', date("d-m-Y", strtotime(($student->registration_date))))
            ->first();
        return $studyYear ;

    }
    public function StudentStudyPlan(){
        $stud_study_year =  $this->academic_number[0].$this->academic_number[1] ;

        $study_year = StudyYear::where('code','like',$stud_study_year.'%')->first();

        $study_plan = $study_year->studyPlans
            ->where('faculty_id','=',$this->faculty_id)
            ->where('department_id','=',$this->department_id)->first() ;

        if($study_plan == null) {
            $study_years = StudyYear::where('code','<',intval($study_year->code))->orderBy('code','desc')->pluck('id');

            foreach ($study_years as $study_year_id){

                $last_study_plans = StudyPlan::where('faculty_id','=',$this->faculty_id)
                    ->where('department_id','=',$this->department_id)
                    ->where('study_year_id',$study_year_id)->first();
                if ($last_study_plans != null)
                    return $last_study_plans ;
            }
        }
        else
        return $study_plan ;
    }
    public function hourPrice(){
        return $this->hasOne(StudentHourPrice::class);
    }
    public function financialBalance(){
        return $this->hasOne(StudentFinancialBalance::class);
    }
    public function modifiedCourses(){
        return $this->hasMany(StudentModifiedCourse::class);
    }
    /*return sum of modified courses hours */
    /*public function modifiedCoursesHours(){
       return $mod_hours = array_sum($this->modifiedCourses->map(function ($m_courses){
            $hours = $this->StudentStudyPlan()->courseDetails($m_courses->course_id)->credit_hours;
            return $hours ;
            })->toArray());
    }*/
    public function getRegisteredHours(){
        return 'R2021';
    }
    public function getCompletedHours(){
        return 'C2021';
    }
    public function getGPA(){
        return 'GPA2021';
    }
    public function getAGPA(){
        return 'AGPA2021';
    }
    public function studentAcademicAllowedHours($study_year_id,$semester_id){
        $student_academic  =  $this->academicSupervision
            ->where('study_year_id',$study_year_id)
            ->where('semester_id',$semester_id)->first();
        $student_academic_hours = $student_academic ? $student_academic->academicStatus->hours : null ;
        return $student_academic_hours;

    }
    public function studentFinanceAllowedHours($study_year_id,$semester_id){
        $student_finance =  $this->financeDetails
            ->where('study_year_id',$study_year_id)
            ->where('semester_id' , $semester_id)->first();
        $student_finance_hours = $student_finance ? $student_finance->hours : 0 ;
        return $student_finance_hours;

    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function academicSupervision(){
        return $this->hasMany(AcademicSupervision::class);
    }
    public function studentOpenedCourses(){
        return $this->hasMany(StudentOpenedCourse::class);
    }
    public function studentRegisteredCourses(){
        return $this->hasMany(StudentRegisteredCourse::class);
    }
    public function StudentCourseHours($course_id){
        $study_plan = $this->StudentStudyPlan();
        $course = $study_plan->courseDetails($course_id);
        return $course->credit_hours;
    }
    public function StudentRegisteredCoursesHours(){
        $hours = 0;
        $registered_courses = $this->studentRegisteredCourses;
        foreach ($registered_courses as $course){
            $hours += $this->StudentCourseHours($course->course_id);
        }
        return $hours;

    }
    public function programSchedule()
    {
        return $this->hasOne(ProgramSchedule::class);
    }
    public function hasOpenedCourse($course_id){
        $opened_courses = $this->studentOpenedCourses;
        if( count($opened_courses) > 0 ) {
            $course_opened = $opened_courses->where('course_id', $course_id)->first();
            return $course_opened;
        }

    }

    /// Polymorph ///
    public function walletable() 
    { 
        return $this->morphOne(ShoppingWallet::class, 'walletable'); 
    }
    /*public function orderable() 
    { 
        return $this->morphOne(ShoppingOrder::class, 'orderable'); 
    }
    public function orderitemable() 
    { 
        return $this->morphOne(ShoppingOrderItem::class, 'orderitemable'); 
    }*/
}
