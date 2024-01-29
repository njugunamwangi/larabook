<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertySearchController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return Property::with('city')
            ->when($request->city, function ($query) use ($request) {
                $query->where('city_id', $request->city);
            })
            ->get();
    }
}
