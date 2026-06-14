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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        if (Schema::hasTable('roles')) {
            $timestamp = now();
        
            DB::table('roles')->insert([
                ['name' => 'STUDENT', 'created_at' => $timestamp, 'updated_at' => $timestamp],
                ['name' => 'FACULTY', 'created_at' => $timestamp, 'updated_at' => $timestamp],
                ['name' => 'ADMIN', 'created_at' => $timestamp, 'updated_at' => $timestamp],
                ['name' => 'SUPER ADMIN', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
