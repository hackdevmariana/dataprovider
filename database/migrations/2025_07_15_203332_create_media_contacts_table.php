<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('media_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_outlet_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['editorial', 'commercial', 'general']);
            $table->string('contact_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_contacts');
    }
};
