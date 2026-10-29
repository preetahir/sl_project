<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('isbn')->unique();
            $table->integer('published_year');
            $table->decimal('price', 8, 2)->default(0.00);
            $table->decimal('rating', 3, 1)->default(5.0);
            $table->string('language')->default('English');
            $table->string('cover_image')->nullable();
            $table->text('summary');
            $table->boolean('featured')->default(false);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->timestamps();
        });

        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->integer('chapter_number');
            $table->string('title');
            $table->longText('content');
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->string('user_name');
            $table->string('user_email')->nullable();
            $table->tinyInteger('rating');
            $table->text('comment');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('chapters');
        Schema::dropIfExists('books');
    }
};

