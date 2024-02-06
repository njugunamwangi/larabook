<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertySearchResource;
use App\Models\Facility;
use App\Models\GeoObject;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertySearchController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $properties = Property::query()
            ->with([
                'city',
                'apartments.apartmentType',
                'apartments.rooms.beds.bedType'
            ])
            ->when($request->city, function ($query) use ($request) {
                $query->where('city_id', $request->city);
            })
            ->when($request->country, function($query) use ($request) {
                $query->whereHas('city', fn($q) => $q->where('country_id', $request->country));
            })
            ->when($request->geo_object, function($query) use ($request) {
                $geo_object = GeoObject::find($request->geo_object);
                if ($geo_object) {
                    $condition = "(
                        6371 * acos(
                            cos(radians(" . $geo_object->lat . "))
                            * cos(radians(`lat`))
                            * cos(radians(`long`) - radians(" . $geo_object->long . "))
                            + sin(radians(" . $geo_object->lat . ")) * sin(radians(`lat`))
                        ) < 10
                    )";
                    $query->whereRaw($condition);
                }
            })
            ->when($request->adults && $request->children, function($query) use ($request) {
                $query->withWhereHas('apartments', function($query) use ($request) {
                    $query->where('capacity_adults', '>=', $request->adults)
                        ->where('capacity_children', '>=', $request->children)
                        ->orderBy('capacity_adults')
                        ->orderBy('capacity_children')
                        ->take(1);
                });
            })
            ->when($request->facilities, function($query) use ($request) {
                $query->whereHas('facilities', function($query) use ($request) {
                    $query->whereIn('facilities.id', $request->facilities);
                });
            })
            ->get();

        $allFacilities = $properties->pluck('facilities')->flatten();
        $facilities = $allFacilities->unique('name')
            ->mapWithKeys(function ($facility) use ($allFacilities) {
                return [$facility->name => $allFacilities->where('name', $facility->name)->count()];
            })
            ->sortDesc();

        return [
            'properties' => PropertySearchResource::collection($properties),
            'facilities' => $facilities,
        ];
    }
}
