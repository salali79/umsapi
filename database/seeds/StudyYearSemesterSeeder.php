<?php

use Illuminate\Database\Seeder;

use App\Models\StudyYearSemester;

class StudyYearSemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('study_year_semesters')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $study_semesters = [
            ['study_year_id' => 4, 'semester_id' => 1, 'beginning' => null, 'end' => null ],
            ['study_year_id' => 4, 'semester_id' => 2, 'beginning' => null, 'end' => null ],
            ['study_year_id' => 4, 'semester_id' => 3, 'beginning' => null, 'end' => null ],

        ];

        for ($i = 0; $i < count($study_semesters); $i++) {
            StudyYearSemester::create($study_semesters[$i]);
        }

    }
}
