<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends Model
{
    protected $fillable = [
        'full_name',
        'employee_code',
        'email',
        'phone',
        'department_id',
        'position',
        'date_of_birth',
        'address',
        'avatar',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'employee_project')
            ->withPivot('role', 'joined_date', 'left_date')
            ->withTimestamps();
    }
}
