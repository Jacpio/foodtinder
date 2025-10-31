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
        Schema::create('types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        if (! Schema::hasTable('types')) {
            return;
        }

        $now = now();
        DB::table('types')->upsert([
            ['name' => 'category', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'cuisine',  'created_at' => $now, 'updated_at' => $now],
            ['name' => 'flavour',  'created_at' => $now, 'updated_at' => $now],
            ['name' => 'other',    'created_at' => $now, 'updated_at' => $now],
        ], ['name'], ['updated_at']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('types')->whereIn('name', ['category','cuisine','flavour','other'])->delete();
        Schema::dropIfExists('types');
    }
};
