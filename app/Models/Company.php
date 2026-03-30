<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'batch',
        'address',
        'contact_person',
        'is_archived'
    ];

    protected $casts = [
        'is_archived' => 'boolean'
    ];

    /**
     * Get students (users) assigned to this company.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
