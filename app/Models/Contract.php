<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    protected $fillable = [
        'employee_id',
        'contract_number',
        'contract_type',
        'classification',
        'industry',
        'project_name',
        'start_date',
        'signing_date',
        'end_date',
        'extension_date',
        'duration_days',
        'contract_content',
        'salary',
        'contract_value',
        'adjusted_value',
        'value_difference',
        'approval_status',
        'status',
        'contract_status',
        'condition_status',
        'investor',
        'legal_entity',
        'advance_payment',
        'file_path',
        'notes',
        'appendix_number',
        'revision_count',
        'extension_count',
    ];

    protected $casts = [
        'start_date' => 'date',
        'signing_date' => 'date',
        'end_date' => 'date',
        'extension_date' => 'date',
        'salary' => 'decimal:2',
        'contract_value' => 'decimal:2',
        'adjusted_value' => 'decimal:2',
        'value_difference' => 'decimal:2',
        'duration_days' => 'integer',
        'revision_count' => 'integer',
        'extension_count' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
