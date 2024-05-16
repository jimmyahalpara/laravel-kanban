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
        Schema::table('cards', function (Blueprint $table) {
            // title column 
            $table->string('title')->nullable();
            // is_completed
            $table->boolean('is_completed')->default(false);
            // deadline datetime 
            $table->dateTime('deadline')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('is_completed');
            $table->dropColumn('deadline');
        });
    }
};
