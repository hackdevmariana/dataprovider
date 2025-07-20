<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('electricity_price_intervals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('electricity_price_id')
                ->constrained()
                ->onDelete('cascade');
            $table->unsignedTinyInteger('interval_index'); // 0..95 si es 15 min, 0..23 si es por hora
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('price_eur_mwh', 8, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('electricity_price_intervals');
    }
};
