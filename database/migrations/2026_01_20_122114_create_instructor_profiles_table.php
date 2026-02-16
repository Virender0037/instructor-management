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
        Schema::create('instructor_profiles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
        $table->string('phone', 30)->nullable();
        $table->text('address')->nullable();
        $table->text('bio')->nullable();
        $table->unsignedTinyInteger('experience_years')->nullable();
        $table->string('specialization', 120)->nullable();
        $table->date('dob')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_profiles');
    }
};
