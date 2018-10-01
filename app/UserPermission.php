<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserPermission extends Authenticatable
{
    // Table
    protected $table = 'user_permissions';
    protected $primaryKey = 'id';
    // Put other elements here.
    public $timestamps = true;
}
