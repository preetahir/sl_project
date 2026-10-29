<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\XmlFeedController;

/*
|--------------------------------------------------------------------------
| Web Routes (Laravel 11)
|--------------------------------------------------------------------------
| Demonstrates Named Routes, Route Groups, Middleware & Route Model Binding
*/

Route::get('/', [BookController::class, 'index'])->name('home');
Route::get('/books/{book:slug}', [BookController::class, 'show'])->name('books.show');
Route::get('/reader/{book}/{chapter?}', [BookController::class, 'reader'])->name('books.reader');
Route::post('/books/{book}/reviews', [BookController::class, 'storeReview'])->name('reviews.store');

// XML Feeds & Syndication
Route::get('/feed.xml', [XmlFeedController::class, 'rss'])->name('xml.rss');
Route::get('/sitemap.xml', [XmlFeedController::class, 'sitemap'])->name('xml.sitemap');
Route::get('/xml/catalog', [XmlFeedController::class, 'export'])->name('xml.export');

// Admin CMS Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/books/create', [AdminController::class, 'create'])->name('books.create');
    Route::post('/books', [AdminController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit', [AdminController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [AdminController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [AdminController::class, 'destroy'])->name('books.destroy');
    
    // Chapter Management
    Route::get('/books/{book}/chapters', [AdminController::class, 'chapters'])->name('chapters.index');
    Route::post('/books/{book}/chapters', [AdminController::class, 'storeChapter'])->name('chapters.store');
    Route::delete('/chapters/{chapter}', [AdminController::class, 'destroyChapter'])->name('chapters.destroy');

    // XML Studio in Laravel
    Route::get('/xml-studio', [AdminController::class, 'xmlStudio'])->name('xml.studio');
    Route::post('/xml-import', [AdminController::class, 'xmlImport'])->name('xml.import');
});

