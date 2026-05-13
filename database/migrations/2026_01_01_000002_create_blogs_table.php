<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index();
            $table->string('slug')->unique()->index();
            $table->string('short_description', 300);
            $table->longText('content');
            $table->string('image')->nullable();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamp('published_at')->index();
            $table->timestamps();

            $table->index(['category_id', 'published_at'], 'idx_blogs_category_published');
        });

        DB::statement('ALTER TABLE blogs ADD FULLTEXT idx_blogs_search (title, short_description)');
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
