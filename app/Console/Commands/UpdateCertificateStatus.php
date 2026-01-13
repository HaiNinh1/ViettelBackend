<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Certificate;

class UpdateCertificateStatus extends Command
{
    protected $signature = 'certificates:update-status';
    protected $description = 'Tự động cập nhật trạng thái chứng chỉ hết hạn';

    public function handle()
    {
        $certificates = Certificate::whereNotNull('expiry_date')->get();
        
        $updated = 0;
        foreach ($certificates as $certificate) {
            $oldStatus = $certificate->status;
            $certificate->updateExpiryStatus();
            
            if ($oldStatus !== $certificate->status) {
                $updated++;
            }
        }

        $this->info("Đã cập nhật trạng thái cho {$updated} chứng chỉ.");
        return 0;
    }
}
