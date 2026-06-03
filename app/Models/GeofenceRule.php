<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeofenceRule extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'latitude', 'longitude', 'radius_meters', 'warning_message', 'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_meters' => 'integer',
        'is_active' => 'boolean',
    ];

    public function challenges(): HasMany
    {
        return $this->hasMany(TypingChallenge::class, 'geofence_rule_id');
    }
}
