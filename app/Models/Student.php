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

    protected $appends = ['image_path','image_thumb_path','full_name'];

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
    public function finalTranscript(){
        return $this->hasOne(FinalTranscript::class);
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
        where('beginning','<=',$student->registration_date)
            ->where('end','>=',$student->registration_date)
            ->first();
        return $studyYear ;

    }
    public function StudentStudyPlan(){
        $stud_study_year =  $this->academic_number[0].$this->academic_number[1] ;
        $study_year = StudyYear::
        query()->where('code','like',$stud_study_year.'%')->first();
        $study_plan = $study_year->studyPlan ;
        return $study_plan ;
    }

    public function hourPrice(){
        return $this->hasOne(StudentHourPrice::class);
    }
    public function studentRegistration(){
        return $this->hasMany(StudentRegistration::class);
    }
    public function financialBalance(){
        return $this->hasOne(StudentFinancialBalance::class);
    }
    public function modifiedCourses(){
        return $this->hasMany(StudentModifiedCourse::class);
    }
    /*return sum of modified courses hours */
    public function modifiedCoursesHours(){
       return $mod_hours = array_sum($this->modifiedCourses->map(function ($m_courses){
            $hours = $this->StudentStudyPlan()->courseDetails($m_courses->course_id)->credit_hours;
            return $hours ;
            })->toArray());
    }
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
    public function getAcademicAllowedHors(){

    }
    public function getFinancialAllowedHors(){

    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
