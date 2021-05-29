<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class SqlStudentTranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('student_translations')->truncate();
        $path = public_path('sql_data/ums_table_student_translations.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);
    }
}
