<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Certificate extends Model
{
    protected $fillable = [
        'employee_id',
        'certificate_type_id',
        'certificate_number',
        'issued_by',
        'issued_date',
        'expiry_date',
        'status',
        'file_path',
        'notes',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function certificateType(): BelongsTo
    {
        return $this->belongsTo(CertificateType::class);
    }

    // Scope để lọc chứng chỉ còn hạn
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'Còn hạn');
    }

    // Scope để lọc chứng chỉ hết hạn
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', 'Hết hạn');
    }

    // Scope để lọc chứng chỉ sắp hết hạn
    public function scopeExpiringSoon(Builder $query): Builder
    {
        return $query->where('status', 'Sắp hết hạn');
    }

    // Method để cập nhật trạng thái dựa trên ngày hết hạn
    public function updateExpiryStatus(): void
    {
        if (!$this->expiry_date) {
            return;
        }

        $today = Carbon::today();
        $expiryDate = Carbon::parse($this->expiry_date);
        $daysUntilExpiry = $today->diffInDays($expiryDate, false);

        if ($daysUntilExpiry < 0) {
            $this->status = 'Hết hạn';
        } elseif ($daysUntilExpiry <= 30) {
            $this->status = 'Sắp hết hạn';
        } else {
            $this->status = 'Còn hạn';
        }

        $this->save();
    }
}
