<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeofenceRule;
use Illuminate\Http\Request;

class AdminGeofenceController extends Controller
{
    public function index()
    {
        $rules = GeofenceRule::latest()->paginate(15);
        return view('admin.geofence.index', compact('rules'));
    }

    public function create()
    {
        return view('admin.geofence.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:1',
            'warning_message' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        GeofenceRule::create($data);

        return redirect()->route('admin.geofence.index')->with('success', 'Geofence rule created.');
    }

    public function edit(GeofenceRule $geofence)
    {
        return view('admin.geofence.edit', compact('geofence'));
    }

    public function update(Request $request, GeofenceRule $geofence)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:1',
            'warning_message' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $geofence->update($data);

        return redirect()->route('admin.geofence.index')->with('success', 'Geofence rule updated.');
    }

    public function destroy(GeofenceRule $geofence)
    {
        $geofence->delete();
        return redirect()->route('admin.geofence.index')->with('success', 'Geofence rule deleted.');
    }
}
