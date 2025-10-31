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
        Schema::create('parameters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('type_id')->constrained('types')->cascadeOnDelete();
            $table->float('value')->unsigned()->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['name', 'type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parameters');
    }
};
