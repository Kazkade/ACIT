<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    // Table
    protected $table = 'maintenance_logs';
    protected $primaryKey = 'id';
    // Put other elements here.
    public $timestamps = true;
}