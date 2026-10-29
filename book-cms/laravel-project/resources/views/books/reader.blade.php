<!DOCTYPE html>
<html lang="en" data-reader-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $currentChapter->title ?? 'Reader' }} - {{ $book->title }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/reader.css') }}">
</head>
<body class="reader-mode">
    <!-- Reading Progress Bar -->
    <div id="reading-progress-bar" class="reading-progress-bar"></div>

    <header class="reader-navbar">
        <div class="reader-nav-left">
            <a href="{{ route('books.show', $book->slug) }}" class="reader-back-btn">&larr; Exit Reader</a>
            <span class="reader-book-title">{{ $book->title }}</span>
        </div>
        <div class="reader-nav-right">
            <button class="reader-tool-btn" id="btn-theme-toggle" title="Switch Theme">🌗</button>
            <button class="reader-tool-btn" id="btn-font-smaller" title="Smaller font">A-</button>
            <button class="reader-tool-btn" id="btn-font-larger" title="Larger font">A+</button>
            <button class="reader-tool-btn" id="btn-fullscreen" title="Fullscreen">⛶</button>
        </div>
    </header>

    <div class="reader-layout">
        <!-- Sidebar Navigation -->
        <aside class="reader-sidebar">
            <h4 class="sidebar-heading">Chapters</h4>
            <ul class="reader-chapter-list">
                @foreach($allChapters as $ch)
                    <li class="{{ $ch->id === $currentChapter->id ? 'active' : '' }}">
                        <a href="{{ route('books.reader', [$book->id, $ch->chapter_number]) }}">
                            Chapter {{ $ch->chapter_number }}: {{ $ch->title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </aside>

        <!-- Chapter Content Body -->
        <main class="reader-content" id="reader-content-body">
            <article class="chapter-article">
                <header class="chapter-header">
                    <span class="chapter-kicker">Chapter {{ $currentChapter->chapter_number }}</span>
                    <h1 class="chapter-title">{{ $currentChapter->title }}</h1>
                    <p class="chapter-author">From <em>{{ $book->title }}</em> by {{ $book->author->name ?? 'Author' }}</p>
                </header>

                <div class="chapter-text typography-body">
                    {!! nl2br(e($currentChapter->content)) !!}
                </div>
            </article>
        </main>
    </div>

    <script src="{{ asset('js/reader.js') }}"></script>
</body>
</html>
