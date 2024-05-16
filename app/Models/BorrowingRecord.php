<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowingRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrowed_at',
        'returned_at',
        'book_id',
        'patron_id',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function patron()
    {
        return $this->belongsTo(Patron::class);
    }
}
