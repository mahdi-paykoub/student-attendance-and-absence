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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('photo')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('gender', ['male', 'female']);
            $table->string('father_name')->nullable();
            $table->string('national_code')->unique();
            $table->string('mobile_student');
            $table->foreignId('grade_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('major_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete();

            $table->string('province')->nullable();
            $table->string('city')->nullable();

            $table->foreignId('consultant_id')->nullable()
                ->constrained('advisors')
                ->nullOnDelete();
            $table->foreignId('referrer_id')->nullable()
                ->constrained('advisors')
                ->nullOnDelete();
            $table->date('custom_date')->nullable();
            $table->date('birthday')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile_father')->nullable();
            $table->string('mobile_mother')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('seat_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
