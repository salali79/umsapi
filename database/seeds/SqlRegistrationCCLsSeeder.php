<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SqlRegistrationCCLsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('registration_c_c_ls')->truncate();
        $path = public_path('sql_data/ums_table_registration_c_c_ls.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);
    }
}
