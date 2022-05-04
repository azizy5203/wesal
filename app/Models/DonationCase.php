<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationCase extends Model
{
    use HasFactory;
    protected $fillable = ['amount' , 'currency', 'case_id'];

    public function user()
    {
        return $this->belongsToMany(User::class,"favourite_cases","case_id","user_id");
    }

    public function usersDonated()
    {
        return $this->belongsToMany(User::class,"donation_operations","case_id","user_id")->withPivot("amount", "currency");
    }

    public function categories()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class,'organization_id');
    }
}
