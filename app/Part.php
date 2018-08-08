<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    // Table
    protected $table = 'parts';
    protected $primaryKey = 'id';
    protected $part_name = 'part_name';
    protected $part_serial = 'part_serial';
    protected $part_version = 'part_version';
    protected $part_color = 'part_color';
    protected $part_weight = 'part_weight';
    protected $part_cleaned = 'part_cleaned';
    protected $part_quantity = 'part_quantity';
    public $timestamps = true;
}
