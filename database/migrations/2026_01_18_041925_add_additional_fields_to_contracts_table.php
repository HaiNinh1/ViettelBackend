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
            // Classification and Industry
            $table->string('classification')->nullable()->after('contract_type'); // Phân loại
            $table->string('industry')->nullable()->after('classification'); // Ngành nghề
            $table->string('project_name')->nullable()->after('industry'); // Tên dự án
            
            // Additional Dates
            $table->date('signing_date')->nullable()->after('start_date'); // Ngày ký
            $table->date('extension_date')->nullable()->after('end_date'); // Ngày gia hạn
            $table->integer('duration_days')->nullable()->after('extension_date'); // Thời gian (days)
            
            // Contract Content and Values
            $table->text('contract_content')->nullable()->after('duration_days'); // Nội dung hợp đồng
            $table->decimal('contract_value', 15, 2)->nullable()->after('contract_content'); // Giá trị hợp đồng
            $table->decimal('adjusted_value', 15, 2)->nullable()->after('contract_value'); // Giá trị sau điều chỉnh
            $table->decimal('value_difference', 15, 2)->nullable()->after('adjusted_value'); // Chênh lệch
            
            // Approval and Status
            $table->string('approval_status')->nullable()->after('value_difference'); // Phê duyệt
            $table->string('contract_status')->nullable()->after('status'); // Trạng thái (e.g., "Chờ tiếp nhận")
            $table->string('condition_status')->nullable()->after('contract_status'); // Tình trạng (e.g., "Đúng tiến độ")
            
            // Investor and Legal
            $table->string('investor')->nullable()->after('condition_status'); // Chủ đầu tư
            $table->string('legal_entity')->nullable()->after('investor'); // Pháp nhân
            
            // Payment and Notes
            $table->enum('advance_payment', ['Có', 'Không'])->nullable()->after('legal_entity'); // Tạm ứng
            $table->text('notes')->nullable()->after('file_path'); // Ghi chú
            
            // Appendix and Counts
            $table->string('appendix_number')->nullable()->after('notes'); // Số phụ lục
            $table->integer('revision_count')->default(0)->after('appendix_number'); // Số lần
            $table->integer('extension_count')->default(0)->after('revision_count'); // Số lần gia hạn
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'classification',
                'industry',
                'project_name',
                'signing_date',
                'extension_date',
                'duration_days',
                'contract_content',
                'contract_value',
                'adjusted_value',
                'value_difference',
                'approval_status',
                'contract_status',
                'condition_status',
                'investor',
                'legal_entity',
                'advance_payment',
                'notes',
                'appendix_number',
                'revision_count',
                'extension_count',
            ]);
        });
    }
};
