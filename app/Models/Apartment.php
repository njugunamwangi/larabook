<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Apartment extends Model
{
    use HasFactory;
    use HasEagerLimit;

    protected $guarded = [];

    public function property(): BelongsTo {
        return $this->belongsTo(Property::class);
    }

    public function apartmentType(): BelongsTo {
        return $this->belongsTo(ApartmentType::class);
    }

    public function rooms() : HasMany {
        return $this->hasMany(Room::class);
    }

    public function beds() : HasManyThrough
    {
        return $this->hasManyThrough(Bed::class, Room::class);
    }

    public function facilities() : BelongsToMany {
        return $this->belongsToMany(Facility::class);
    }

    public function prices(): HasMany {
        return $this->hasMany(ApartmentPrice::class);
    }

    public function bedsList(): Attribute
    {
        $allBeds = $this->beds;
        $bedsByType = $allBeds->groupBy('bedType.name');
        $bedsList = '';
        if ($bedsByType->count() == 1) {
            $bedsList = $allBeds->count() . ' ' . str($bedsByType->keys()[0])->plural($allBeds->count());
        } else if ($bedsByType->count() > 1) {
            $bedsList = $allBeds->count() . ' ' . str('bed')->plural($allBeds->count());
            $bedsListArray = [];
            foreach ($bedsByType as $bedType => $beds) {
                $bedsListArray[] = $beds->count() . ' ' . str($bedType)->plural($beds->count());
            }
            $bedsList .= ' ('.implode(', ' , $bedsListArray) .')';
        }

        return new Attribute(
            get: fn () => $bedsList
        );
    }
}
