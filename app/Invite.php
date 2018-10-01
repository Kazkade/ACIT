<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    // Table
    protected $table = 'user_invites';
    protected $primaryKey = 'id';
    // Put other elements here.
    public $timestamps = true;
}
