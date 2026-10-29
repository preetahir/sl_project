@extends('layouts.app')

@section('title', 'Admin Dashboard - LibrisCMS')

@section('content')
<div class="admin-dashboard-page">
    <div class="section-container">
        <div class="admin-header-flex">
            <div>
                <span class="badge badge-admin">CMS Management Panel</span>
                <h1 class="admin-title">Book Content Management Dashboard</h1>
                <p class="admin-subtitle">Manage books, chapters, categories, and XML synchronization.</p>
            </div>
            <div class="admin-actions-bar">
                <a href="{{ route('admin.books.create') }}" class="btn btn-primary">➕ Add New Book</a>
                <a href="{{ route('admin.xml.studio') }}" class="btn btn-secondary">🛠️ XML Studio</a>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="admin-metrics-grid">
            <div class="metric-card">
                <span class="metric-number">{{ $stats['total_books'] }}</span>
                <span class="metric-label">Total Books</span>
            </div>
            <div class="metric-card">
                <span class="metric-number">{{ $stats['total_chapters'] }}</span>
                <span class="metric-label">Chapters Authored</span>
            </div>
            <div class="metric-card">
                <span class="metric-number">{{ $stats['total_categories'] }}</span>
                <span class="metric-label">Genres</span>
            </div>
            <div class="metric-card">
                <span class="metric-number">⭐ {{ $stats['average_rating'] }}</span>
                <span class="metric-label">Average Score</span>
            </div>
        </div>

        <!-- Books Table -->
        <div class="admin-table-card">
            <div class="card-header">
                <h3>Catalog Inventory</h3>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Cover</th>
                            <th>Title &amp; Author</th>
                            <th>ISBN</th>
                            <th>Genre</th>
                            <th>Year</th>
                            <th>Price</th>
                            <th>Rating</th>
                            <th>Chapters</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $b)
                            <tr>
                                <td>
                                    <img src="{{ asset('images/covers/' . ($b->cover_image ?? 'default_book.svg')) }}" width="40" height="56" class="table-thumbnail" alt="">
                                </td>
                                <td>
                                    <strong>{{ $b->title }}</strong><br>
                                    <small class="text-muted">{{ $b->author->name ?? 'Unknown' }}</small>
                                </td>
                                <td><code>{{ $b->isbn }}</code></td>
                                <td><span class="badge badge-category">{{ $b->category->name ?? 'General' }}</span></td>
                                <td>{{ $b->published_year }}</td>
                                <td>${{ number_format($b->price, 2) }}</td>
                                <td>⭐ {{ number_format($b->rating, 1) }}</td>
                                <td>
                                    <a href="{{ route('admin.chapters.index', $b->id) }}" class="badge-link">
                                        {{ $b->chapters_count }} Chapters &rarr;
                                    </a>
                                </td>
                                <td class="actions-cell">
                                    <a href="{{ route('admin.books.edit', $b->id) }}" class="btn-action edit" title="Edit">✏️</a>
                                    <form action="{{ route('admin.books.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this book?')" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action delete" title="Delete">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No books in catalog. Click "Add New Book" to begin.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $books->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
