<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('aliases', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['nickname', 'stage_name', 'birth_name', 'other'])->default('other');
            $table->boolean('is_primary')->default(false);
            $table->foreignId('person_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('aliases');
    }
};
