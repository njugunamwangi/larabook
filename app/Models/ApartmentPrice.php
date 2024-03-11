<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApartmentPrice extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function apartment(): BelongsTo {
        return $this->belongsTo(Apartment::class);
    }

    public function scopeValidForRange($query, array $range = [])
    {
        return $query->where(function ($query) use ($range) {
            return $query
                // Covers outer bounds
                ->where(function ($query) use ($range) {
                    $query->where('start_date', '>=', reset($range))->where('end_date', '<=', end($range));
                })
                // Covers left and right bound
                ->orWhere(function ($query) use ($range) {
                    $query->whereBetween('start_date', $range)->orWhereBetween('end_date', $range);
                })
                // Covers inner bounds
                ->orWhere(function ($query) use ($range) {
                    $query->where('start_date', '<=', reset($range))
                        ->where('end_date', '>=', end($range));
                });
        });
    }
}
