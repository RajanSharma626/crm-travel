<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Destination;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Hotel::with(['destination', 'location']);

        // Filter by country
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        // Filter by destination
        if ($request->filled('destination_id')) {
            $query->where('destination_id', $request->destination_id);
        }

        // Filter by location
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $hotels = $query->latest()->paginate(12);
        
        // Get countries from destinations table
        $countries = Destination::select('country')
            ->distinct()
            ->whereNotNull('country')
            ->orderBy('country')
            ->pluck('country');
            
        $destinations = Destination::orderBy('name')->get();

        return view('hotels.index', compact('hotels', 'destinations', 'countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $destinations = Destination::orderBy('name')->get();
        
        // Get countries from destinations table
        $countries = Destination::select('country')
            ->distinct()
            ->whereNotNull('country')
            ->orderBy('country')
            ->pluck('country');
            
        return view('hotels.create', compact('destinations', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_no_1' => 'nullable|string|max:20',
            'contact_no_2' => 'nullable|string|max:20',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'country' => 'nullable|string|max:255',
            'destination_id' => 'nullable|exists:destinations,id',
            'location_id' => 'nullable|exists:locations,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Hotel::create($validated);

        return redirect()->route('hotels.index')->with('success', 'Hotel added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Hotel $hotel)
    {
        $hotel->load(['destination', 'location']);
        
        // Return JSON for AJAX requests
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($hotel);
        }
        
        return view('hotels.show', compact('hotel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hotel $hotel)
    {
        $destinations = Destination::orderBy('name')->get();
        $locations = $hotel->destination_id 
            ? Location::where('destination_id', $hotel->destination_id)->orderBy('name')->get() 
            : collect();
        
        // Get countries from destinations table
        $countries = Destination::select('country')
            ->distinct()
            ->whereNotNull('country')
            ->orderBy('country')
            ->pluck('country');
        
        return view('hotels.edit', compact('hotel', 'destinations', 'locations', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hotel $hotel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_no_1' => 'nullable|string|max:20',
            'contact_no_2' => 'nullable|string|max:20',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'country' => 'nullable|string|max:255',
            'destination_id' => 'nullable|exists:destinations,id',
            'location_id' => 'nullable|exists:locations,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $hotel->update($validated);

        return redirect()->route('hotels.index')->with('success', 'Hotel updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hotel $hotel)
    {
        $hotel->delete();
        return redirect()->route('hotels.index')->with('success', 'Hotel deleted successfully!');
    }

    /**
     * Get destinations for a specific country (API endpoint)
     */
    public function getDestinationsByCountry(Request $request, $country)
    {
        $destinations = Destination::where('country', $country)
            ->orderBy('name')
            ->get();
        
        return response()->json($destinations);
    }

    /**
     * Get locations for a specific destination (API endpoint)
     */
    public function getLocationsByDestination(Request $request, $destinationId)
    {
        $locations = Location::where('destination_id', $destinationId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return response()->json($locations);
    }
}
