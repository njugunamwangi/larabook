<?php

namespace App\Models;

use App\Traits\ValidForRange;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApartmentPrice extends Model
{
    use HasFactory;
    use ValidForRange;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function apartment(): BelongsTo {
        return $this->belongsTo(Apartment::class);
    }
}
