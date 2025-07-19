<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cooperatives', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('legal_name')->nullable();
            $table->enum('cooperative_type', ['energy', 'housing', 'agriculture', 'etc']);
            $table->enum('scope', ['local', 'regional', 'national']);
            $table->string('nif')->nullable();
            $table->date('founded_at')->nullable();
            $table->string('phone');
            $table->string('email');
            $table->string('website');
            $table->string('logo_url')->nullable();
            $table->foreignId('image_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('municipality_id')->constrained()->cascadeOnDelete();
            $table->string('address');
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('number_of_members')->nullable();
            $table->string('main_activity');
            $table->boolean('is_open_to_new_members')->default(false);
            $table->string('source')->default('manual');
            $table->foreignId('data_source_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('has_energy_market_access')->default(false);
            $table->string('legal_form')->nullable();
            $table->string('statutes_url')->nullable();
            $table->boolean('accepts_new_installations')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cooperatives');
    }
};
