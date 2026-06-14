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
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        if (Schema::hasTable('statuses')) {
            $timestamp = now();
        
            DB::table('statuses')->insert([
                ['name' => 'preparing', 'created_at' => $timestamp, 'updated_at' => $timestamp],
                ['name' => 'prepared', 'created_at' => $timestamp, 'updated_at' => $timestamp],
                ['name' => 'successful', 'created_at' => $timestamp, 'updated_at' => $timestamp],
                ['name' => 'cancelled', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
