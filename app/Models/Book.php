<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\models\Review;


class Book extends Model
{
    use HasFactory;
    
    // Relationship
    public function reviews(){
        return $this->hasMany(Review::class);
    }

    //Local scope

    public function scopeTitle(Builder $query,string $title)  {
        return $query->where('title','LIKE',"%{$title}%"); // Search Title
    }

    public function scopeWithReviewsCount (Builder $query,$from = null, $to = null){
        return $query->withCount([
            'reviews' => fn(Builder $q) => $this->filterByDate($q,$from,$to)
           ]); // Get count review with Filter recent date 
    }

    public function scopeWithAvgRating(Builder $query,$from = null, $to = null){
        return $query->withAvg([
            'reviews' => fn(Builder $q) => $this->filterByDate($q,$from,$to)
        ],'rating'); // Get count rating with Filter recent date 
    }
  
    public function scopePopular(Builder $query,$from = null, $to = null){
     return $query->withCount([
         'reviews' => fn(Builder $q) => $this->filterByDate($q,$from,$to)
        ])->orderBy('reviews_count','desc'); // Get polular review with Filter recent date 
     }
     
     public function scopeHighestRated(Builder $query,$from = null, $to = null){
        return $query->withAvg([
            'reviews' => fn(Builder $q) => $this->filterByDate($q,$from,$to)
        ],'rating')->orderBy('reviews_avg_rating','desc'); // Get highest rated with Filter recent date 
    }
    
    public function scopeMinReview(Builder $query,int $minReview){
        return $query->having('reviews_count','>=',$minReview); //Get min review 
    } 
    public function scopePopularLastMonth(Builder $query){
        return $query->popular(now()->subMonth(),now())
                     ->highestRated(now()->subMonth(),now())
                     ->minReview(2);
    }

    public function scopePopularLast6Month(Builder $query){
        return $query->popular(now()->subMonths(6),now())
                     ->highestRated(now()->subMonths(6),now())
                     ->minReview(5);
    }

    public function scopeHighestRatedLastMonth(Builder $query){
        return $query->highestRated(now()->subMonth(),now())
                     ->popular(now()->subMonth(),now())
                     ->minReview(2);             
    }

    public function scopeHighestRatedLast6Months(Builder $query){
        return $query->highestRated(now()->subMonths(6),now())
                     ->popular(now()->subMonths(6),now())
                     ->minReview(2);             
    }


    private function filterByDate(builder $query, $from = null, $to = null){
        if($from && !$to){
            $query->where('created_at','>=',$from);
        }
        if(!$from && $to){
            $query->where('created_at','<=',$to);   
        }
        if($from && $to){
            $query->whereBetween('created_at',[$from,$to]);
        }
        return $query;
    } // Filter recent date

    protected static function booted()
    {
        static::updated(fn(Book $book) => cache()->forget("book:{$book->id}"));
        static::deleted(fn(Book $book) => cache()->forget("book:{$book->id}"));

    }
}