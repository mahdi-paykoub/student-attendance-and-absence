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
        Schema::create('student_supporter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // پشتبان فعلی
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_by_id')->nullable()->constrained('users')->onDelete('set null'); // کسی که ارجاع داده
            $table->foreignId('previous_supporter_id')->nullable()->constrained('users')->onDelete('set null'); // پشتبان قبلی در ارجاع
            $table->enum('relation_type', ['assigned', 'referred'])->default('assigned');
            $table->boolean('is_returned')->default(false);

            $table->enum('progress_status', ['pending', 'in_progress', 'done'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_supporter');
    }
};
