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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personnel_id')->constrained('personnels')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('period_id')->constrained('evaluation_periods')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('theory_score', 5, 2)->default(0);
            $table->decimal('practical_score', 5, 2)->default(0);
            $table->decimal('ethics_score', 5, 2)->default(0);
            $table->integer('accident_count')->default(0);
            $table->integer('near_miss_count')->default(0);
            $table->integer('warning_count')->default(0);
            $table->decimal('total_score', 6, 2)->default(0);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['personnel_id', 'period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
