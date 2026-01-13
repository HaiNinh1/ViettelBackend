<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificateType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'validity_period',
        'required_renewal',
    ];

    protected $casts = [
        'required_renewal' => 'boolean',
    ];

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
}
