<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Apartment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function property(): BelongsTo {
        return $this->belongsTo(Property::class);
    }

    public function apartmentType(): BelongsTo {
        return $this->belongsTo(ApartmentType::class);
    }
}
