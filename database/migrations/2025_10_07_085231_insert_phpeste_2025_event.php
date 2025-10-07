<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('events')->insert([
            'id' => 1,
            'title' => 'PHPeste 2025',
            'description' => 'Conferência de PHP no Nordeste',
            'location' => 'Parnaíba, Piauí',
            'start_datetime' => '2025-10-03 17:00:00',
            'end_datetime' => '2025-10-03 20:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
