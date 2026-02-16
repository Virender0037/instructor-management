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
        Schema::table('users', function (Blueprint $table) {
        $table->string('role', 30)->default('instructor')->after('password');
        $table->boolean('status')->default(true)->after('role');
        $table->foreignId('created_by')
            ->nullable()
            ->after('status')
            ->constrained('users')
            ->nullOnDelete();

        $table->index('role');
        $table->index('status');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['role', 'status', 'created_by']);
        });
    }
};
