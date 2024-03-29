<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bed extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function room(): BelongsTo {
        return $this->belongsTo(Room::class);
    }

    public function bedType(): BelongsTo {
        return $this->belongsTo(BedType::class);
    }
}
