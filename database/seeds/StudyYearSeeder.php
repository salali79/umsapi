<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class StudyYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('study_years')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $study_years = [
            ['beginning' => null, 'end' => null, 'name' => '2008-2009','code' => '0809'],
            ['beginning' => null, 'end' => null, 'name' => '2009-2010','code' => '0910'],
            ['beginning' => null, 'end' => null, 'name' => '2010-2011','code' => '1011'],
            ['beginning' => null, 'end' => null, 'name' => '2011-2012','code' => '1112'],
            ['beginning' => null, 'end' => null, 'name' => '2012-2013','code' => '1213'],
            ['beginning' => null, 'end' => null, 'name' => '2013-2014','code' => '1314'],
            ['beginning' => null, 'end' => null, 'name' => '2014-2015','code' => '1415'],
            ['beginning' => null, 'end' => null, 'name' => '2015-2016','code' => '1516'],
            ['beginning' => null, 'end' => null, 'name' => '2016-2017','code' => '1617'],
            ['beginning' => null, 'end' => null, 'name' => '2017-2018','code' => '1718'],
            ['beginning' => null, 'end' => null, 'name' => '2018-2019','code' => '1819'],
            ['beginning' => null, 'end' => null, 'name' => '2019-2020','code' => '1920'],
            ['beginning' => null, 'end' => null, 'name' => '2020-2021','code' => '2021']
        ];
        DB::table('study_years')->insert($study_years);
    }
}
