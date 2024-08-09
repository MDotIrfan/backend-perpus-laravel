<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'summary',
        'image',
        'stok',
        'category_id'
    ];

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function list_borrows()
    {
        return $this->hasMany(Borrow::class, 'book_id');
    }
}
