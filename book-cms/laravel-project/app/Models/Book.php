<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model {
    protected $fillable = [
        'category_id',
        'author_id',
        'title',
        'slug',
        'isbn',
        'published_year',
        'price',
        'rating',
        'language',
        'cover_image',
        'summary',
        'featured',
        'views_count'
    ];

    protected $casts = [
        'featured' => 'boolean',
        'price' => 'decimal:2',
        'rating' => 'decimal:1',
        'published_year' => 'integer'
    ];

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo {
        return $this->belongsTo(Author::class);
    }

    public function chapters(): HasMany {
        return $this->hasMany(Chapter::class)->orderBy('chapter_number', 'asc');
    }

    public function reviews(): HasMany {
        return $this->hasMany(Review::class)->latest();
    }
}

