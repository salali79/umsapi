<?php

namespace App\Models;



class Evaluation extends AppModel
{
    protected $fillable =['percentage_min','percentage_max','point_equivalent_min','point_equivalent_max',
        'estimate','description','created_by','updated_by','deleted_by'] ;
}
