<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $destinations = Destination::with('locations')->latest()->paginate(25);
        return view('destinations.index', compact('destinations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('destinations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:destinations,name',
            'country' => 'nullable|string|max:255',
            'locations' => 'nullable|array',
            'locations.*' => 'nullable|string|max:255',
        ]);

        $destination = Destination::create([
            'name' => $validated['name'],
            'country' => $validated['country'] ?? null,
        ]);

        // Save locations
        if (!empty($validated['locations'])) {
            foreach ($validated['locations'] as $locationName) {
                if (!empty(trim($locationName))) {
                    $destination->locations()->create([
                        'name' => trim($locationName),
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()->route('destinations.index')->with('success', 'Destination added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Destination $destination)
    {
        // Redirect to edit page since there's no dedicated show view
        return redirect()->route('destinations.edit', $destination);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Destination $destination)
    {
        $destination->load('locations');
        return view('destinations.edit', compact('destination'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Destination $destination)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:destinations,name,' . $destination->id,
            'country' => 'nullable|string|max:255',
            'locations' => 'nullable|array',
            'locations.*' => 'nullable|string|max:255',
            'location_ids' => 'nullable|array',
            'location_ids.*' => 'nullable|integer|exists:locations,id',
        ]);

        $destination->update([
            'name' => $validated['name'],
            'country' => $validated['country'] ?? null,
        ]);

        // Handle locations update
        if (isset($validated['locations'])) {
            // Get existing location IDs that should be kept
            $existingLocationIds = $validated['location_ids'] ?? [];
            
            // Delete locations that are not in the list
            $destination->locations()->whereNotIn('id', $existingLocationIds)->delete();

            // Update or create locations
            foreach ($validated['locations'] as $index => $locationName) {
                if (!empty(trim($locationName))) {
                    $locationId = $existingLocationIds[$index] ?? null;
                    if ($locationId) {
                        // Update existing location
                        $destination->locations()->where('id', $locationId)->update([
                            'name' => trim($locationName),
                        ]);
                    } else {
                        // Create new location
                        $destination->locations()->create([
                            'name' => trim($locationName),
                            'is_active' => true,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('destinations.index')->with('success', 'Destination updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Destination $destination)
    {
        $destination->delete();
        return redirect()->route('destinations.index')->with('success', 'Destination deleted successfully!');
    }

    /**
     * Get locations for a specific destination (API endpoint)
     */
    public function getLocations(Request $request, $destinationId)
    {
        $destination = Destination::findOrFail($destinationId);
        $locations = $destination->locations()->where('is_active', true)->orderBy('name')->get();
        
        return response()->json($locations);
    }
}
