<?php

namespace App\Models;


class Bank extends AppModel
{
   protected $fillable = [
       'name','account_number','description',
       'financial_commander_name','financial_commander_title',
       'created_by','updated_by','deleted_by'];

}
