<?php

namespace App\Http\Controllers;

use App\Models\SportsField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class SportsFieldController extends Controller
{
    public function index(Request $request)
    {
        $query = SportsField::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_verified')) {
            $query->where('is_verified', filter_var($request->is_verified, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->has(['lat', 'lng', 'radius'])) {
            $lat = $request->lat;
            $lng = $request->lng;
            $radius = $request->radius;

            $query->selectRaw("*,
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude)))) AS distance",
                [$lat, $lng, $lat])
            ->having("distance", "<", $radius)
            ->orderBy("distance");
        }

        $fields = $query->with('reviews')->get();

        return response()->json([
            'status_code' => 200,
            'message' => 'Filtered sports fields retrieved',
            'data' => $fields
        ]);
    }

    public function show(SportsField $sportsField)
    {
        $sportsField->load('reviews');
        $sportsField->load(['reviews.user', 'owner', 'facilities']);

        return response()->json([
            'status_code' => 200,
            'message' => 'Sports field found',
            'data' => $sportsField
        ]);
    }

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'location' => 'required|string|max:255',
        'type' => 'required|string|max:100',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    try {
        $data = $request->only(['name', 'location', 'type', 'latitude', 'longitude']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('field-images', 'public');
            $data['image_path'] = $path;
        }

        $data['is_verified'] = false;
        $data['rating'] = 0;

        $field = SportsField::create($data);

        return response()->json([
            'status_code' => 201,
            'message' => 'Lapangan berhasil ditambahkan',
            'data' => $field,
            'image_url' => asset('storage/' . $field->image_path),
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status_code' => 500,
            'message' => 'Failed to create sports field',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    public function update(Request $request, SportsField $sportsField)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:100',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $data = $request->only('name', 'location', 'type');
            unset($request['is_verified']);
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('public/field-images');
                $filename = basename($path);
                $data['image_path'] = 'field-images/' . $filename;
            }

            $sportsField->update($data);

            return response()->json([
                'status_code' => 200,
                'message' => 'Sports field updated successfully',
                'data' => $sportsField
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Failed to update sports field',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(SportsField $sportsField)
    {
        try {
            $sportsField->delete();

            return response()->json([
                'status_code' => 200,
                'message' => 'Sports field deleted successfully',
                'data' => null
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Failed to delete sports field',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadPhoto(Request $request, $id)
    {
        $request->validate([
            'image_path' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $field = SportsField::findOrFail($id);

            $path = $request->file('image_path')->store('public/field-images');
            $filename = basename($path);

            $field->image_path = 'field-images/' . $filename;
            $field->save();

            return response()->json([
                'status_code' => 200,
                'message' => 'Foto berhasil diunggah',
                'data' => [
                    'image_url' => asset('storage/field-images/' . $filename),
                    'field' => $field
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Upload gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function recommendation()
    {
        $fields = SportsField::where('is_verified', 1)
            ->orderByDesc('rating')
            ->take(5)
            ->get();

        return response()->json([
            'status_code' => 200,
            'message' => 'Recommended fields retrieved',
            'data' => $fields,
        ]);
    }
    public function verify(Request $request, $id)
{
    $user = auth()->user();
    if (!$user || $user->role !== 'admin') {
        return response()->json([
            'status_code' => 403,
            'message' => 'Hanya admin yang dapat memverifikasi lapangan'
        ], 403);
    }

    $field = SportsField::findOrFail($id);

    if ($field->is_verified) {
        return response()->json([
            'status_code' => 200,
            'message' => 'Lapangan sudah diverifikasi sebelumnya'
        ]);
    }

    $field->is_verified = true;
    $field->save();

    return response()->json([
        'status_code' => 200,
        'message' => 'Lapangan berhasil diverifikasi',
        'data' => $field
    ]);
}

}
