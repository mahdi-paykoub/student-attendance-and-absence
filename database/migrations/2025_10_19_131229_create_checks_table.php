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
        Schema::create('checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_product_id')->constrained('student_product')->cascadeOnDelete();
            $table->string('owner');       // نام صاحب چک
            $table->string('phone');       // شماره موبایل صاحب چک
            $table->string('image')->nullable(); // عکس چک
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checks');
    }
};
