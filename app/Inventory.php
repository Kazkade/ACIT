<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    // Table
    protected $table = 'inventories';
    protected $primaryKey = 'id';
    // Put other elements here.
    public $timestamps = true;
}