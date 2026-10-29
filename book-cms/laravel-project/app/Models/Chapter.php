<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chapter extends Model {
    protected $fillable = [
        'book_id',
        'chapter_number',
        'title',
        'content'
    ];

    public function book(): BelongsTo {
        return $this->belongsTo(Book::class);
    }
}
