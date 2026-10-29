<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Book;

/*
|--------------------------------------------------------------------------
| API Routes (Laravel 11)
|--------------------------------------------------------------------------
| Demonstrates RESTful JSON Web Services & API Resources
*/

Route::get('/books', function (Request $request) {
    $query = Book::with(['category', 'author']);

    if ($request->has('search')) {
        $term = '%' . $request->search . '%';
        $query->where(function($q) use ($term) {
            $q->where('title', 'like', $term)
              ->orWhere('isbn', 'like', $term);
        });
    }

    if ($request->has('category')) {
        $query->whereHas('category', function($q) use ($request) {
            $q->where('slug', $request->category);
        });
    }

    return response()->json([
        'status' => 'success',
        'data' => $query->paginate(10)
    ]);
});

Route::get('/books/{id}', function ($id) {
    $book = Book::with(['category', 'author', 'chapters', 'reviews'])->find($id);
    if (!$book) {
        return response()->json(['status' => 'error', 'message' => 'Book not found'], 404);
    }
    return response()->json(['status' => 'success', 'data' => $book]);
});

