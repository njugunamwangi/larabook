<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertySearchResource;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Property $property, Request $request)
    {
        $property->load('apartments.facilities');

        if ($request->adults && $request->children) {
            $property->load(['apartments' => function ($query) use ($request) {
                $query->where('capacity_adults', '>=', $request->adults)
                    ->where('capacity_children', '>=', $request->children)
                    ->orderBy('capacity_adults')
                    ->orderBy('capacity_children');
            }]);
        }

        return new PropertySearchResource($property);
    }
}
