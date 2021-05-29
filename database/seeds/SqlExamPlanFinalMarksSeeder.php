<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class SqlExamPlanFinalMarksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('exam_plan_final_marks')->truncate();
        $path = public_path('sql_data/ums_table_exam_plan_final_marks.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);
    }
}
