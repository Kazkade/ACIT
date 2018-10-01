<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Printer extends Model
{
    // Table
    protected $table = 'printers';
    protected $primaryKey = 'id';
    // Put other elements here.
    public $timestamps = true;
}