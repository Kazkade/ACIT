<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Filament extends Model
{
    // Table
    protected $table = 'filaments';
    protected $primaryKey = 'id';
    // Put other elements here.
    public $timestamps = true;
}
