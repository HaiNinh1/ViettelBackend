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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('certificate_type_id')->constrained()->onDelete('cascade');
            $table->string('certificate_number')->unique();
            $table->string('issued_by')->nullable();
            $table->date('issued_date');
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['Còn hạn', 'Hết hạn', 'Sắp hết hạn'])->default('Còn hạn');
            $table->string('file_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
