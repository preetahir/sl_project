@extends('layouts.app')

@section('title', 'Manage Chapters: ' . $book->title . ' - LibrisCMS')

@section('content')
<div class="admin-chapters-page">
    <div class="section-container">
        <nav class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">&larr; Back to Dashboard</a> &rsaquo;
            <span>{{ $book->title }} (Chapters)</span>
        </nav>

        <div class="admin-header-flex">
            <div>
                <h1 class="admin-title">Chapters in "{{ $book->title }}"</h1>
                <p class="admin-subtitle">Add or edit individual chapters for reader mode.</p>
            </div>
        </div>

        <div class="validator-layout">
            <!-- Add Chapter Form -->
            <div class="form-container-card">
                <h3>Add / Update Chapter</h3>
                <form action="{{ route('admin.chapters.store', $book->id) }}" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group flex-1">
                            <label for="chapter_number">Chapter Number *</label>
                            <input type="number" name="chapter_number" id="chapter_number" value="{{ ($chapters->max('chapter_number') ?? 0) + 1 }}" min="1" required class="form-input">
                        </div>
                        <div class="form-group flex-2">
                            <label for="title">Chapter Title *</label>
                            <input type="text" name="title" id="title" placeholder="e.g. In Which We Meet the Protagonist" required class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="content">Chapter Content (HTML or Plain Text) *</label>
                        <textarea name="content" id="content" rows="12" required class="form-textarea" placeholder="Write or paste the chapter manuscript here..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Chapter</button>
                    </div>
                </form>
            </div>

            <!-- Existing Chapters List -->
            <div class="form-container-card">
                <h3>Existing Chapters ({{ $chapters->count() }})</h3>
                <ul class="admin-chapter-list">
                    @forelse($chapters as $ch)
                        <li class="chapter-item">
                            <div>
                                <strong>Chapter {{ $ch->chapter_number }}: {{ $ch->title }}</strong>
                                <br><small class="text-muted">{{ Str::words(strip_tags($ch->content), 15) }}</small>
                            </div>
                            <div>
                                <form action="{{ route('admin.chapters.destroy', $ch->id) }}" method="POST" onsubmit="return confirm('Delete this chapter?')" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action delete" title="Delete">🗑️</button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="text-muted">No chapters added yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
