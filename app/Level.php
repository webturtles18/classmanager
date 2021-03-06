<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = ['title'];
    
    public function classManager(){
        return $this->belongsTo('App\ClassManager');
    }
}
