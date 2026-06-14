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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        if (Schema::hasTable('payments')) {
            $timestamp = now();
        
            DB::table('payments')->insert([
                ['name' => 'gcash', 'created_at' => $timestamp, 'updated_at' => $timestamp],
                ['name' => 'school fee', 'created_at' => $timestamp, 'updated_at' => $timestamp],
                ['name' => 'cash on hand', 'created_at' => $timestamp, 'updated_at' => $timestamp],
                ['name' => 'payroll', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
