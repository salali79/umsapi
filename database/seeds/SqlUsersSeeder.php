<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class SqlUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        $path = public_path('sql_data/ums_table_users.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);
    }
}
