<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    protected $casts = [
        'details' => 'array',
    ];
    
    protected $fillable = [
        'recipient_id','profile','account_holder_name','currency',
        'country','type','details','user','active'
    ];
    
}
