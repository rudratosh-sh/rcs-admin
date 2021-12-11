<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RcsAccount extends Model
{   
    use HasFactory;
    protected $table = 'rcs_accounts';
    protected $guarded = [];
}
