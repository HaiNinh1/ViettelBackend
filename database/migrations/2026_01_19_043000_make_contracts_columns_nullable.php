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
        Schema::table('contracts', function (Blueprint $table) {
            // Make all columns nullable to allow import with missing data
            $table->string('contract_number')->nullable()->change();
            $table->string('contract_type')->nullable()->change();
            $table->string('classification')->nullable()->change();
            $table->string('industry')->nullable()->change();
            $table->string('project_name')->nullable()->change();
            $table->date('start_date')->nullable()->change();
            $table->date('signing_date')->nullable()->change();
            $table->date('end_date')->nullable()->change();
            $table->date('extension_date')->nullable()->change();
            $table->integer('duration_days')->nullable()->change();
            $table->text('contract_content')->nullable()->change();
            $table->decimal('salary', 15, 2)->nullable()->change();
            $table->decimal('contract_value', 15, 2)->nullable()->change();
            $table->decimal('adjusted_value', 15, 2)->nullable()->change();
            $table->decimal('value_difference', 15, 2)->nullable()->change();
            $table->string('approval_status')->nullable()->change();
            $table->string('status')->nullable()->change();
            $table->string('contract_status')->nullable()->change();
            $table->string('condition_status')->nullable()->change();
            $table->string('investor')->nullable()->change();
            $table->string('legal_entity')->nullable()->change();
            $table->string('advance_payment')->nullable()->change();
            $table->string('file_path')->nullable()->change();
            $table->text('notes')->nullable()->change();
            $table->string('appendix_number')->nullable()->change();
            $table->integer('revision_count')->nullable()->change();
            $table->integer('extension_count')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed
    }
};
