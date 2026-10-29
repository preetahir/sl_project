# LibrisCMS - Laravel 11 Blueprint & Architecture Guide

This directory contains the official **Laravel 11 / 10** architecture implementation of the **Responsive Book Content Management System**. It is structured for academic coursework submission, viva demonstrations, and enterprise Laravel development.

---

## 🏛️ Laravel Concepts Demonstrated

1. **MVC Architecture (Model-View-Controller)**:
   - **Controllers**: `app/Http/Controllers/BookController.php`, `AdminController.php`, `XmlFeedController.php`.
   - **Models**: `app/Models/Book.php`, `Category.php`, `Author.php`, `Chapter.php`, `Review.php`.
   - **Views**: Blade templates using `@extends('layouts.app')`, `@section('content')`, `@yield()`.

2. **Eloquent ORM & Relational Modeling**:
   - `Book::belongsTo(Category::class)`
   - `Book::hasMany(Chapter::class)`
   - `Book::hasMany(Review::class)`
   - Eager loading with `Book::with(['category', 'author'])` to prevent N+1 query performance bottlenecks.

3. **Routing (`routes/web.php` & `routes/api.php`)**:
   - Named routes (`route('books.show')`)
   - Route Model Binding (`/books/{book:slug}`)
   - Route Groups and Prefixes (`Route::prefix('admin')->name('admin.')->group(...)`)
   - RESTful JSON API endpoints with pagination (`/api/books`).

4. **Database Migrations & Seeders**:
   - Declarative DDL migrations with foreign key constraints, cascading deletes, and column indexing.
   - `database/seeders/BookDatabaseSeeder.php` parses `xml/catalog.xml` into the database.

5. **Form Requests & Validation**:
   - Backend validation rules for ISBN, email, required fields, and rating constraints.
   - Cross-Site Request Forgery (CSRF) protection using `@csrf`.

---

## 🚀 How to Run in a Standard Laravel Environment

```bash
# 1. Install Composer dependencies
composer install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Run database migrations and seeders
php artisan migrate --seed

# 4. Start local development server
php artisan serve
```
Open [http://127.0.0.1:8000](http://127.0.0.1:8000) in your browser.

