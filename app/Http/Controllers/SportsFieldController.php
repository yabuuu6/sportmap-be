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

        // Filter berdasarkan jenis olahraga
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
        ]);

        try {
            $field = SportsField::create($request->only('name', 'location', 'type'));

            return response()->json([
                'status_code' => 201,
                'message' => 'Sports field created successfully',
                'data' => $field
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Failed to create sports field',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, SportsField $sportsField)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:100',
        ]);

        try {
            $sportsField->update($request->only('name', 'location', 'type'));

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
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $field = SportsField::findOrFail($id);

            // Simpan gambar ke folder storage/app/public/field-images
            $path = $request->file('image')->store('public/field-images');
            $filename = basename($path);

            // Simpan nama file ke kolom image_path
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

}
