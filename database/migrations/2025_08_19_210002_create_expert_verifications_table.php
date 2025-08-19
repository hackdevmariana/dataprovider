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
        Schema::create('expert_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('expertise_area'); // solar, wind, legal, financial, technical, etc.
            $table->enum('verification_level', ['basic', 'advanced', 'professional', 'expert'])->default('basic');
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'expired'])->default('pending');
            $table->json('credentials')->nullable(); // Submitted credentials/documents
            $table->json('verification_documents')->nullable(); // Document URLs/references
            $table->text('expertise_description');
            $table->integer('years_experience')->default(0);
            $table->json('certifications')->nullable(); // Professional certifications
            $table->json('education')->nullable(); // Educational background
            $table->json('work_history')->nullable(); // Relevant work experience
            $table->decimal('verification_fee', 8, 2)->default(0); // Fee paid for verification
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('verification_notes')->nullable(); // Notes from verifier
            $table->text('rejection_reason')->nullable();
            $table->integer('verification_score')->nullable(); // Score given by verifier (1-100)
            $table->boolean('is_public')->default(true); // Whether verification is public
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'expertise_area']);
            $table->index(['user_id', 'status']);
            $table->index(['expertise_area', 'verification_level']);
            $table->index(['status', 'submitted_at']);
            $table->index(['verified_by', 'verified_at']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expert_verifications');
    }
};