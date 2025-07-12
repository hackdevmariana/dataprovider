<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('label')->nullable();
            $table->morphs('related'); // related_type y related_id
            $table->enum('type', ['wikipedia', 'imdb', 'official', 'twitter', 'instagram', 'other'])->default('other');
            $table->boolean('is_primary')->default(false);
            $table->boolean('opens_in_new_tab')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
