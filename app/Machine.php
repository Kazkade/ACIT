<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    // Table
    protected $table = 'machines';
    protected $primaryKey = 'id';
    // Put other elements here.
    public $timestamps = true;
}