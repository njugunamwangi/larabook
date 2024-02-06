<?php

namespace App\Models;

use App\Observers\PropertyObserver;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Property extends Model implements HasMedia
{
    use HasFactory;
    use HasEagerLimit;
    use InteractsWithMedia;

    protected $guarded = [];

    public function city(): BelongsTo {
        return $this->belongsTo(City::class);
    }

    public function apartments(): HasMany {
        return $this->hasMany(Apartment::class);
    }

    public function address(): Attribute {
        return new Attribute(get: fn() => $this->address_street . ', ' . $this->address_postcode . ', ' . $this->city->name);
    }

    public static function booted()
    {
        parent::booted();

        self::observe(PropertyObserver::class);
    }

    public function facilities() : BelongsToMany {
        return $this->belongsToMany(Facility::class);
    }
}
