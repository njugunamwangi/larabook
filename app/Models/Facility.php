<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Facility extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(FacilityCategory::class, 'category_id');
    }

    public function apartments() : BelongsToMany {
        return $this->belongsToMany(Facility::class);
    }

    public function properties() : BelongsToMany {
        return $this->belongsToMany(Property::class);
    }
}
