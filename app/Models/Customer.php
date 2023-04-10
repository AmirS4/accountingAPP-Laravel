<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'number', 'email', 'details'
    ];

    protected $table = 'customers';

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function turnover()
    {
        return $this->hasOne(Turnover::class);
    }

}
