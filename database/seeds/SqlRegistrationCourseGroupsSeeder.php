<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SqlRegistrationCourseGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('registration_course_groups')->truncate();
        $path = public_path('sql_data/ums_table_registration_course_groups.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);
    }
}
