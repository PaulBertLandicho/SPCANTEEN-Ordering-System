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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        if (Schema::hasTable('categories')) {
            $timestamp = now();
        
            DB::table('categories')->insert([
                ['name' => 'Breakfast', 'created_at' => $timestamp, 'updated_at' => $timestamp],
                ['name' => 'Lunch', 'created_at' => $timestamp, 'updated_at' => $timestamp],
                ['name' => 'Snack', 'created_at' => $timestamp, 'updated_at' => $timestamp],
                ['name' => 'Beverage', 'created_at' => $timestamp, 'updated_at' => $timestamp],
                ['name' => 'Dinner', 'created_at' => $timestamp, 'updated_at' => $timestamp],
                ['name' => 'Dessert', 'created_at' => $timestamp, 'updated_at' => $timestamp],
                ['name' => 'Healthy', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
