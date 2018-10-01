<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    // Table
    protected $table = 'deliveries';
    protected $primaryKey = 'id';
    // Put other elements here.
    public $timestamps = true;
}
