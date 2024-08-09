<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'load_date',
        'borrow_date',
        'book_id',
        'user_id'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function book()
    {
        return $this->hasOne(Book::class, 'id', 'book_id');
    }
}
