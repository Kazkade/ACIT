<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    // Table
    protected $table = 'locations';
    protected $primaryKey = 'id';
    // Put other elements here.
    public $timestamps = true;
}
