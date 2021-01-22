<?php

namespace App\Models;



class Equivalent extends AppModel
{
    protected $fillable =['hundred_equivalent_min','hundred_equivalent_max','char_equivalent',
        'point_equivalent', 'description','created_by','updated_by','deleted_by'];
}
