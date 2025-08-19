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
        Schema::create('user_privileges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('privilege_type'); // posting, voting, moderation, verification, etc.
            $table->string('scope')->default('global'); // global, topic, cooperative, project
            $table->unsignedBigInteger('scope_id')->nullable(); // ID of the specific scope (topic_id, cooperative_id, etc.)
            $table->integer('level')->default(1); // Privilege level (1-5)
            $table->boolean('is_active')->default(true);
            $table->json('permissions')->nullable(); // Specific permissions granted
            $table->json('limits')->nullable(); // Usage limits (posts per day, votes per hour, etc.)
            $table->integer('reputation_required')->default(0); // Minimum reputation needed
            $table->timestamp('granted_at')->useCurrent();
            $table->timestamp('expires_at')->nullable(); // Some privileges might be temporary
            $table->foreignId('granted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('reason')->nullable(); // Why this privilege was granted
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'privilege_type']);
            $table->index(['user_id', 'scope', 'scope_id']);
            $table->index(['privilege_type', 'level']);
            $table->index(['is_active', 'expires_at']);
            $table->index('reputation_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_privileges');
    }
};