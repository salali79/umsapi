<?php

use Illuminate\Database\Seeder;
use App\Models\StudyPlanCourseLevel;

class StudyPlanLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('study_plan_course_levels')->truncate();
        DB::table('study_plan_course_level_translations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $levels =[[] ,[],[],[] ,[],[],[] ,[],[],[] ,[],[]];
        $levelTrans =[
            ['name'=> 'المستوى 1 ','locale'=>'ar'],
            ['name'=> 'المستوى 2 ','locale'=>'ar'],
            ['name'=> 'المستوى 3 ','locale'=>'ar'],
            ['name'=> 'المستوى 4 ','locale'=>'ar'],
            ['name'=> 'المستوى 5 ','locale'=>'ar'],
            ['name'=> 'المستوى 6 ','locale'=>'ar'],
            ['name'=> 'المستوى 7 ','locale'=>'ar'],
            ['name'=> 'المستوى 8 ','locale'=>'ar'],
            ['name'=> 'المستوى 9 ','locale'=>'ar'],
            ['name'=> 'المستوى 10 ','locale'=>'ar'],
            ['name'=> 'المستوى 11 ','locale'=>'ar'],
            ['name'=> 'المستوى 12 ','locale'=>'ar'],
            ];
        for($i=0 ; $i < count($levels); $i++)
        {
            StudyPlanCourseLevel::create($levels[$i])->translations()->create($levelTrans[$i]);
        }
    }
}
