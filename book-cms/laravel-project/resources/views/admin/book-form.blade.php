@extends('layouts.app')

@section('title', isset($book) ? 'Edit Book - LibrisCMS' : 'Add New Book - LibrisCMS')

@section('content')
<div class="admin-form-page">
    <div class="section-container">
        <nav class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">&larr; Back to Dashboard</a>
        </nav>

        <div class="form-container-card">
            <h2>{{ isset($book) ? 'Edit Book Record' : 'Create New Book Record' }}</h2>
            <p class="text-muted">Enter the publication metadata. It will be stored in SQLite and synced with XML.</p>

            <form action="{{ isset($book) ? route('admin.books.update', $book->id) : route('admin.books.store') }}" method="POST">
                @csrf
                @if(isset($book))
                    @method('PUT')
                @endif

                <div class="form-row">
                    <div class="form-group flex-2">
                        <label for="title">Book Title *</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $book->title ?? '') }}" required class="form-input">
                    </div>
                    <div class="form-group flex-1">
                        <label for="isbn">ISBN Code *</label>
                        <input type="text" name="isbn" id="isbn" value="{{ old('isbn', $book->isbn ?? '') }}" placeholder="978-0-123456-47-2" required class="form-input">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id">Genre / Category *</label>
                        <select name="category_id" id="category_id" required class="form-select">
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ (old('category_id', $book->category_id ?? '') == $c->id) ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="author_id">Author *</label>
                        <select name="author_id" id="author_id" required class="form-select">
                            @foreach($authors as $a)
                                <option value="{{ $a->id }}" {{ (old('author_id', $book->author_id ?? '') == $a->id) ? 'selected' : '' }}>
                                    {{ $a->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="published_year">Year *</label>
                        <input type="number" name="published_year" id="published_year" min="1800" max="2099" value="{{ old('published_year', $book->published_year ?? date('Y')) }}" required class="form-input">
                    </div>

                    <div class="form-group">
                        <label for="price">Price ($) *</label>
                        <input type="number" step="0.01" min="0" name="price" id="price" value="{{ old('price', $book->price ?? '0.00') }}" required class="form-input">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group flex-1">
                        <label for="language">Language</label>
                        <input type="text" name="language" id="language" value="{{ old('language', $book->language ?? 'English') }}" class="form-input">
                    </div>
                    <div class="form-group flex-1">
                        <label for="cover_image">Cover Image Filename</label>
                        <input type="text" name="cover_image" id="cover_image" value="{{ old('cover_image', $book->cover_image ?? 'default_book.svg') }}" class="form-input">
                    </div>
                    <div class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="featured" value="1" {{ old('featured', $book->featured ?? false) ? 'checked' : '' }}>
                            Featured Book
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="summary">Synopsis / Summary *</label>
                    <textarea name="summary" id="summary" rows="5" required class="form-textarea">{{ old('summary', $book->summary ?? '') }}</textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">{{ isset($book) ? 'Save Changes' : 'Create Book' }}</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
