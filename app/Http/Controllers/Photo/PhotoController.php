<?php

namespace App\Http\Controllers\Photo;

use App\Events\PhotoUploaded;
use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PhotoController extends Controller
{
    use AuthorizesRequests;

    public function upload(Request $request, Album $album)
    {
        $this->authorize('update', $album);

        if ($request->hasFile('images')) {
            $phpError = $request->file('images')[0]->getErrorMessage();
            \Log::error("Alasan PHP menolak file: " . $phpError);
        }

        $validated = $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $uploadedCount = 0;
        $uploadedPhotos = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // 1. Ekstrak data SEBELUM file dipindahkan!
                $mimeType = $image->getMimeType();
                $fileSize = $image->getSize();

                // Ambil dimensi gambar (width & height)
                $dimensions = getimagesize($image->getPathname());
                $width = $dimensions ? $dimensions[0] : null;
                $height = $dimensions ? $dimensions[1] : null;

                // 2. Pindahkan file
                $filename = time().'_'.$image->hashName();
                $directory = 'uploads/album_photos/'.$album->id;

                $image->move(public_path($directory), $filename);
                $fullPath = $directory.'/'.$filename;

                // 3. Simpan ke Database
                $photo = Photo::create([
                    'album_id' => $album->id,
                    'user_id' => auth()->id(),
                    'title' => $request->input('title'),
                    'description' => $request->input('description'),
                    'image_path' => $fullPath,
                    'image_url' => asset($fullPath),
                    'mime_type' => $mimeType,
                    'file_size' => $fileSize,
                    'width' => $width,       // Data dimensi ditambahkan
                    'height' => $height,     // Data dimensi ditambahkan
                    'order' => Photo::where('album_id', $album->id)->max('order') + 1,
                ]);

                $uploadedPhotos[] = $photo;
                $uploadedCount++;

                // TRIGGER LARAVEL REVERB BROADCAST
                broadcast(new PhotoUploaded($photo))->toOthers();
            }
        }

        $album->update(['photo_count' => $album->photos()->count()]);

        return response()->json([
            'success' => true,
            'message' => "{$uploadedCount} foto berhasil diupload!",
            'photos' => $uploadedPhotos,
        ], 201);
    }

    public function destroy(Photo $photo)
    {
        $album = $photo->album;
        $this->authorize('update', $album);

        if ($photo->image_path && file_exists(public_path($photo->image_path))) {
            unlink(public_path($photo->image_path));
        }

        $photo->delete();
        $album->update(['photo_count' => $album->photos()->count()]);

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil dihapus!',
        ]);
    }

    public function reorder(Request $request, Album $album)
    {
        $this->authorize('update', $album);

        $validated = $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'integer|exists:photos,id',
        ]);

        foreach ($validated['photos'] as $order => $photoId) {
            Photo::where('id', $photoId)
                ->where('album_id', $album->id)
                ->update(['order' => $order]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan foto berhasil diperbarui!',
        ]);
    }
}
