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

        Schema::create('messages', function (Blueprint $table) {
        $table->id();

        $table->foreignId('sender_id')
            ->constrained('users')
            ->cascadeOnDelete();

        $table->foreignId('receiver_id')
            ->constrained('users')
            ->cascadeOnDelete();

        $table->string('subject', 200)->nullable();
        $table->text('body');

        $table->timestamp('sent_at')->useCurrent();
        $table->timestamp('read_at')->nullable();

        // optional “delete for me” (recommended)
        $table->timestamp('sender_deleted_at')->nullable();
        $table->timestamp('receiver_deleted_at')->nullable();

        $table->timestamps();

        // indexes for inbox/sent performance
        $table->index(['receiver_id', 'read_at']);
        $table->index(['receiver_id', 'sent_at']);
        $table->index(['sender_id', 'sent_at']);
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
