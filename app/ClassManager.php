<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassManager extends Model
{
    protected $fillable = [
        'college_id','title','contact_no','email','price','description','syllabus'
    ];
    
    public function user(){
        return $this->belongsTo('App\User');
    }
    
    public function college(){
        return $this->belongsTo('App\College');
    }
    
    public function levels(){
        return $this->hasMany('App\Level');
    }
    
    /**
    * Users table filter
    *
    * @return array
    */
    public function scopeFilter($query, $params)
    {
        if ( isset($params['search']) && !empty($params['search']) ){
            $search = $params['search'];
            $query->where('title', 'like', "%".$search."%");
            $query->orWhereHas('college', function($q) use($search){
                $q->where('name', 'like', "%".$search."%");
            });
            $query->orWhereHas('levels', function($q) use($search){
                $q->where('title', 'like', "%".$search."%");
            });
        }
        return $query;
    }
    
    public static function deleteById($id){
        return self::where('id', $id)->delete();
    }
}
