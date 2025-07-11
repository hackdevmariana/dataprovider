<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('point_of_interest_tag', function (Blueprint $table) {
            $table->foreignId('point_of_interest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['point_of_interest_id', 'tag_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('point_of_interest_tag');
    }
};
