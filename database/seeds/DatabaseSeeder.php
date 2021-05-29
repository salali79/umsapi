<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SqlMigrationsSeeder::class);
        $this->call(SqlUsersSeeder::class);

        $this->call(SqlEquivalentsSeeder::class);
        $this->call(SqlEvaluationsSeeder::class);

        $this->call(SqlStudyYearsSeeder::class);
        $this->call(SqlSemestersSeeder::class);
        $this->call(SqlSemesterTranslationsSeeder::class);
        $this->call(SqlStudyYearSemestersSeeder::class);

        $this->call(SqlRegisterWaysSeeder::class);
        $this->call(SqlRegisterWayTranslationsSeeder::class);
        $this->call(SqlRegisterParamsSeeder::class);
        $this->call(SqlRegisterParamTranslationsSeeder::class);

        $this->call(SqlFacultiesSeeder::class);
        $this->call(SqlFacultyTranslationsSeeder::class);
        $this->call(SqlDepartmentsSeeder::class);
        $this->call(SqlDepartmentTranslationsSeeder::class);

        $this->call(SqlFolderTypesSeeder::class);
        $this->call(SqlFolderTypeTranslationsSeeder::class);
        $this->call(SqlFolderFilesSeeder::class);
        $this->call(SqlFolderFileTranslationsSeeder::class);
        $this->call(SqlFolderFileFolderTypeSeeder::class);


        $this->call(SqlStudentsSeeder::class);

        $this->call(SqlStudentTranslationsSeeder::class);
           // $this->call(SqlStudentContactsSeeder::class);
          //$this->call(SqlStudentContactTranslationsSeeder::class);
         //$this->call(SqlStudentEmergenciesSeeder::class);
        //$this->call(SqlStudentEmergencyTranslationsSeeder::class);
       //$this->call(SqlStudentRegisterParamsSeeder::class);

        $this->call(SqlCoursesSeeder::class);
        $this->call(SqlCourseTranslationsSeeder::class);

        $this->call(SqlStudyPlanCourseLevelsSeeder::class);
        $this->call(SqlStudyPlanCourseLevelTranslationsSeeder::class);

        $this->call(SqlStudyPlansSeeder::class);
        $this->call(SqlStudyPlanDetailsSeeder::class);

        $this->call(SqlFinanceAccountsSeeder::class);

        $this->call(SqlExamPlansSeeder::class);
        $this->call(SqlExamPlanCoursesSeeder::class);
        $this->call(SqlExamPlanFinalMarksSeeder::class);

        $this->call(SqlBanksSeeder::class);
        $this->call(SqlStudentHourPricesSeeder::class);

        $this->call(SqlStudentDepositRequestsSeeder::class);

        $this->call(SqlRegistrationPlansSeeder::class);
        $this->call(SqlRegistrationCoursesSeeder::class);
        $this->call(SqlRegistrationCourseGroupsSeeder::class);
        $this->call(SqlRegistrationCGLsSeeder::class);
        $this->call(SqlRegistrationCourseCategoriesSeeder::class);
        $this->call(SqlRegistrationCCLsSeeder::class);


        $this->call(SqlSemesterRegisterFeesSeeder::class);

        $this->call(SqlStudentFinancialBalancesSeeder::class);

        //$this->call(SqlStudentModifiedCoursesSeeder::class);



          //$this->call(UserSeeder::class);
         //$this->call(StudyYearSeeder::class);
        //->call(SemesterSeeder::class);
       //$this->call(StudyYearSemesterSeeder::class);
      //$this->call(FolderFileSeeder::class);
        //$this->call(FolderTypeSeeder::class);
        //$this->call(FolderFileTypeSeeder::class);
        //$this->call(RegisterWaySeeder::class);
        //$this->call(RegisterParamsSeeder::class);
        //$this->call(StudyPlanLevelSeeder::class);
    }
}
