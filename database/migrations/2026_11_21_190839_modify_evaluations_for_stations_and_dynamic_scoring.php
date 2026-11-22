<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropUnique(['personnel_id', 'period_id']);
        });

        Schema::table('evaluations', function (Blueprint $table) {
            $table->foreignId('station_id')
                ->nullable()
                ->constrained('stations')
                ->nullOnDelete();
            $table->text('description')->nullable();
            $table->date('evaluation_date')->nullable();
        });

        DB::table('evaluations')->update([
            'evaluation_date' => DB::raw('date(created_at)'),
        ]);

        Schema::table('evaluations', function (Blueprint $table) {
            $table->date('evaluation_date')->nullable(false)->change();
            $table->decimal('theory_score', 5, 2)->nullable()->change();
            $table->decimal('practical_score', 5, 2)->nullable()->change();
            $table->decimal('ethics_score', 5, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropForeign(['station_id']);
            $table->dropColumn(['station_id', 'description', 'evaluation_date']);
        });

        foreach (['theory_score', 'practical_score', 'ethics_score'] as $column) {
            DB::table('evaluations')->whereNull($column)->update([$column => 0]);
        }

        Schema::table('evaluations', function (Blueprint $table) {
            $table->decimal('theory_score', 5, 2)->nullable(false)->default(0)->change();
            $table->decimal('practical_score', 5, 2)->nullable(false)->default(0)->change();
            $table->decimal('ethics_score', 5, 2)->nullable(false)->default(0)->change();

            $table->unique(['personnel_id', 'period_id']);
        });
    }
};
