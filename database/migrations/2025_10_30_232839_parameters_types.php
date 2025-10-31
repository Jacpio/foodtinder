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
        Schema::table('parameters', function (Blueprint $table) {
            if (!Schema::hasColumn('parameters', 'type')) {
                $table->string('type')->nullable()->index();
            }
        });

        if (Schema::hasColumn('parameters', 'type_id')) {
            DB::statement("
                UPDATE parameters
                SET type = (
                    SELECT name FROM types WHERE types.id = parameters.type_id
                )
                WHERE type_id IS NOT NULL
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('parameters', 'type')) {
            Schema::table('parameters', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
