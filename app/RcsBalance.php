<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RcsBalance extends Model
{   
    use HasFactory;
    protected $table = 'rcs_balance';
    protected $fillable = [
        'id','user_id','balance','valid_from','valid_till','assigned'
     ];
}
