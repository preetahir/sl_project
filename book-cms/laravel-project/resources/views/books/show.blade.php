@extends('layouts.app')

@section('title', $book->title . ' - LibrisCMS')

@section('content')
<div class="book-details-page">
    <div class="section-container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Catalog</a> &rsaquo;
            <a href="{{ route('home', ['category' => $book->category->slug ?? '']) }}">{{ $book->category->name ?? 'Category' }}</a> &rsaquo;
            <span>{{ $book->title }}</span>
        </nav>

        <div class="book-detail-grid">
            <div class="book-detail-media">
                <img src="{{ asset('images/covers/' . ($book->cover_image ?? 'default_book.svg')) }}" alt="{{ $book->title }}" class="book-cover-large">
                <div class="action-buttons-stack">
                    @if($book->chapters->isNotEmpty())
                        <a href="{{ route('books.reader', [$book->id, 1]) }}" class="btn btn-primary btn-block">📖 Start Reading</a>
                    @endif
                    <a href="{{ route('xml.export') }}" class="btn btn-outline btn-block">📥 Download XML Metadata</a>
                </div>
            </div>

            <div class="book-detail-content">
                <span class="category-tag">{{ $book->category->name ?? 'General' }}</span>
                <h1 class="book-hero-title">{{ $book->title }}</h1>
                <p class="book-author-lead">Written by <strong>{{ $book->author->name ?? 'Unknown' }}</strong></p>

                <div class="meta-specs-strip">
                    <div class="spec-item">
                        <span class="spec-label">Rating</span>
                        <span class="spec-val">⭐ {{ number_format($book->rating, 1) }}</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Published</span>
                        <span class="spec-val">{{ $book->published_year }}</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">ISBN</span>
                        <span class="spec-val"><code>{{ $book->isbn }}</code></span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Price</span>
                        <span class="spec-val text-success">${{ number_format($book->price, 2) }}</span>
                    </div>
                </div>

                <div class="synopsis-box">
                    <h2>Synopsis</h2>
                    <p>{{ $book->summary }}</p>
                </div>

                <!-- Table of Contents -->
                <div class="toc-card">
                    <h3>Chapters in this Volume ({{ $book->chapters->count() }})</h3>
                    @if($book->chapters->isNotEmpty())
                        <ul class="chapters-list">
                            @foreach($book->chapters as $ch)
                                <li>
                                    <a href="{{ route('books.reader', [$book->id, $ch->chapter_number]) }}">
                                        <span class="ch-num">Chapter {{ $ch->chapter_number }}</span>
                                        <span class="ch-name">{{ $ch->title }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No chapters available yet.</p>
                    @endif
                </div>

                <!-- Reviews Section -->
                <div class="reviews-section">
                    <h3>Community Reviews</h3>
                    <form action="{{ route('reviews.store', $book->id) }}" method="POST" class="review-form">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label for="user_name">Your Name</label>
                                <input type="text" name="user_name" id="user_name" required class="form-input">
                            </div>
                            <div class="form-group">
                                <label for="rating">Rating (1 to 5)</label>
                                <select name="rating" id="rating" class="form-select">
                                    <option value="5">⭐⭐⭐⭐⭐ (5 Stars)</option>
                                    <option value="4">⭐⭐⭐⭐ (4 Stars)</option>
                                    <option value="3">⭐⭐⭐ (3 Stars)</option>
                                    <option value="2">⭐⭐ (2 Stars)</option>
                                    <option value="1">⭐ (1 Star)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="comment">Your Review</label>
                            <textarea name="comment" id="comment" rows="3" required class="form-textarea"></textarea>
                        </div>
                        <button type="submit" class="btn btn-secondary">Submit Review</button>
                    </form>

                    <div class="reviews-list">
                        @forelse($book->reviews as $rev)
                            <div class="review-item">
                                <div class="review-header">
                                    <strong>{{ $rev->user_name }}</strong>
                                    <span class="stars">{{ str_repeat('⭐', $rev->rating) }}</span>
                                </div>
                                <p class="review-text">{{ $rev->comment }}</p>
                                <small class="text-muted">{{ $rev->created_at->diffForHumans() }}</small>
                            </div>
                        @empty
                            <p class="text-muted">Be the first to review this book!</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
