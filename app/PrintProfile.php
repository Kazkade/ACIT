<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrintProfile extends Model
{
    // Table
    protected $table = 'print_profiles';
    protected $primaryKey = 'id';
    // Put other elements here.
    public $timestamps = true;
}