<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Overage extends Model
{
    // Table
    protected $table = 'overages';
    protected $primaryKey = 'id';
    // Put other elements here.
    public $timestamps = true;
}
