<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable= ['title','author','published_year','isbn','price','genre','quantity','description'];
    public function scopeSearch($query,$keyword){
        return $query->where('title','like',"%$keyword%")
                    ->orWhere('author','like',"%$keyword%")
                    ->orWhere('isbn', 'like', "%$keyword%")
                    ->orWhere('genre', 'like', "%$keyword%");
    }
    public function scopeFilter($query,array $filters)
    {
        if(isset($filters['genre'])){
            $query->where('genre',$filters['genre']);
        }
        if(isset($filters['published_year'])){
            $query->where("published_year",$filters['published_year']);
        }
        if(isset($filters['available'])){
            $query->where('quantity',">",0);
        }
        return $query;
    }
}
