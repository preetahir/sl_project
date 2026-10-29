@extends('layouts.app')

@section('title', 'Browse Book Catalog - LibrisCMS')

@section('content')
<section class="hero-section">
    <div class="hero-container">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="badge-dot"></span>
                <span>Laravel 11 Edition • MVC &amp; Eloquent ORM</span>
            </div>
            <h1 class="hero-title">
                Explore The World of <span class="gradient-text">Timeless Books</span>
            </h1>
            <p class="hero-subtitle">
                Built with modern Laravel MVC architecture, Eloquent ORM, Blade templating, and XML syndication feeds.
            </p>
            <form action="{{ route('home') }}" method="GET" class="hero-search-form">
                <div class="search-input-wrapper">
                    <input 
                        type="search" 
                        name="search" 
                        class="search-input" 
                        placeholder="Search by title, author, or ISBN..." 
                        value="{{ request('search') }}"
                    >
                </div>
                <button type="submit" class="btn btn-primary search-submit-btn">Search Books</button>
            </form>
        </div>
    </div>
</section>

<section class="catalog-section" id="catalog-section">
    <div class="section-container">
        <!-- Category Filters -->
        <div class="category-pills-bar">
            <a href="{{ route('home') }}" class="category-pill {{ !request('category') ? 'active' : '' }}">
                All Categories
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('home', ['category' => $cat->slug]) }}" class="category-pill {{ request('category') === $cat->slug ? 'active' : '' }}">
                    {{ $cat->name }} ({{ $cat->books_count }})
                </a>
            @endforeach
        </div>

        <!-- Books Grid -->
        <div class="books-grid" id="books-grid">
            @forelse($books as $book)
                <article class="book-card">
                    <div class="book-card-cover">
                        <img src="{{ asset('images/covers/' . ($book->cover_image ?? 'default_book.svg')) }}" alt="{{ $book->title }}">
                        <span class="category-tag">{{ $book->category->name ?? 'Literature' }}</span>
                    </div>
                    <div class="book-card-body">
                        <h3 class="book-title">
                            <a href="{{ route('books.show', $book->slug) }}">{{ $book->title }}</a>
                        </h3>
                        <p class="book-author">By {{ $book->author->name ?? 'Unknown Author' }}</p>
                        <p class="book-summary">{{ Str::limit($book->summary, 120) }}</p>
                        
                        <div class="book-meta-footer">
                            <span class="book-rating">⭐ {{ number_format($book->rating, 1) }}</span>
                            <span class="book-price">${{ number_format($book->price, 2) }}</span>
                        </div>
                        
                        <div class="book-card-actions">
                            <a href="{{ route('books.show', $book->slug) }}" class="btn btn-sm btn-outline">Details</a>
                            <a href="{{ route('books.reader', $book->id) }}" class="btn btn-sm btn-primary">Read Now</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="empty-state">
                    <p>No books found matching your criteria.</p>
                    <a href="{{ route('home') }}" class="btn btn-secondary">Clear Filters</a>
                </div>
            @endforelse
        </div>

        <div class="pagination-wrapper">
            {{ $books->links() }}
        </div>
    </div>
</section>
@endsection
