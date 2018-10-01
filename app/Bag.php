<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bag extends Model
{
    // Table
    protected $table = 'bags';
    protected $primaryKey = 'id';
    // Put other elements here.
    public $timestamps = true;
}
