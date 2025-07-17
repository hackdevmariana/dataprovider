<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->morphs('subject'); // country_id, municipality_id, etc.
            $table->string('key');     // e.g. 'gdp', 'population'
            $table->decimal('value', 20, 4);
            $table->year('year');
            $table->foreignId('data_source_id')->nullable()->constrained('data_sources');
            $table->string('unit')->nullable();
            $table->decimal('confidence_level', 5, 2)->nullable();
            $table->text('source_note')->nullable();
            $table->boolean('is_projection')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stats');
    }
};
