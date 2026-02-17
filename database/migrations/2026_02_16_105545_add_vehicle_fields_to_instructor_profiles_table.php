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
        Schema::table('instructor_profiles', function (Blueprint $table) {
        $table->string('telefoonnummer', 30)->nullable()->after('user_id');
        $table->string('wagennummer', 30)->nullable()->after('telefoonnummer');
        $table->enum('auto', ['Volkswagen', 'Mercedes', 'Audi'])->nullable()->after('wagennummer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructor_profiles', function (Blueprint $table) {
        $table->dropColumn(['telefoonnummer', 'wagennummer', 'auto']);
        });
    }
};
