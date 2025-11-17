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
        Schema::create('supporter_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supporter_id')->constrained()->onDelete('cascade'); // پشتبان فعلی
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_by_id')->nullable()->constrained('supporters')->onDelete('set null'); // کسی که ارجاع داده
            $table->foreignId('previous_supporter_id')->nullable()->constrained('supporters')->onDelete('set null'); // پشتبان قبلی در ارجاع
            $table->enum('status', ['assigned', 'referred', 'resolved'])->default('assigned');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supporter_student');
    }
};
