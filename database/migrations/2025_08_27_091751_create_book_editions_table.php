<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_editions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->string('edition_number');
            $table->string('format');
            $table->string('publisher');
            $table->date('publication_date');
            $table->string('isbn')->nullable();
            $table->integer('pages');
            $table->string('cover_type')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('currency')->default('EUR');
            $table->json('special_features')->nullable();
            $table->string('translator')->nullable();
            $table->string('illustrator')->nullable();
            $table->boolean('is_limited')->default(false);
            $table->integer('print_run')->nullable();
            $table->timestamps();

            $table->unique(['book_id', 'edition_number', 'format']);
            $table->index(['publication_date']);
            $table->index(['is_limited']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_editions');
    }
};
