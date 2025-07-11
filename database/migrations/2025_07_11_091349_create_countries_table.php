<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('iso_alpha2', 2)->unique();
            $table->string('iso_alpha3', 3)->unique();
            $table->string('iso_numeric', 3)->nullable();
            $table->string('demonym')->nullable();
            $table->string('official_language')->nullable();
            $table->string('currency_code')->nullable();
            $table->string('phone_code')->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->string('flag_url')->nullable();
            $table->bigInteger('population')->nullable();
            $table->decimal('gdp_usd', 15, 2)->nullable();
            $table->string('region_group')->nullable(); // UE, Mercosur, etc.
            $table->decimal('area_km2', 10, 2)->nullable();
            $table->integer('altitude_m')->nullable();
            $table->foreignId('timezone_id')->nullable()->constrained('timezones');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('countries');
    }
};
