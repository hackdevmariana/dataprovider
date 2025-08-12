<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->string('address')->nullable()->change();
            $table->decimal('latitude', 10, 7)->nullable()->change();
            $table->decimal('longitude', 10, 7)->nullable()->change();
            $table->enum('venue_type', ['auditorium', 'park', 'square', 'club', 'online', 'other'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->string('address')->nullable(false)->change();
            $table->decimal('latitude', 10, 7)->nullable(false)->change();
            $table->decimal('longitude', 10, 7)->nullable(false)->change();
            $table->enum('venue_type', ['auditorium', 'park', 'square', 'club', 'online', 'other'])->nullable(false)->change();
        });
    }
};
