<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    // Table
    protected $table = 'transfers';
    protected $primaryKey = 'id';
    // Put other elements here.
    public $timestamps = true;
}
