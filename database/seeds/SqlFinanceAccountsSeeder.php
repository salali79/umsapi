<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SqlFinanceAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('finance_accounts')->truncate();
        $path = public_path('sql_data/ums_table_finance_accounts.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);
    }
}
