<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            // whose document (usually instructor). NULL allowed for system/general docs if needed.
            $table->foreignId('owner_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // who uploaded it (admin or instructor)
            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title', 200)->nullable();
            $table->string('category', 50)->default('general'); // certificate, agreement, training_material, etc.

            // type metadata
            $table->string('file_type', 20);   // pdf | image
            $table->string('mime_type', 100);  // application/pdf, image/png, etc.
            $table->string('extension', 10);   // pdf, png, jpg, jpeg, webp

            // file metadata
            $table->string('original_name', 255);
            $table->string('file_path', 500);  // storage path
            $table->string('storage_disk', 30)->default('private'); // private|local|s3 etc.
            $table->unsignedBigInteger('file_size'); // bytes

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // helpful indexes
            $table->index('owner_id');
            $table->index('uploaded_by');
            $table->index('category');
            $table->index('file_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
