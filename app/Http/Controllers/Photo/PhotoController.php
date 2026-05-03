<?php

namespace App\Http\Controllers\Photo;

use App\Events\PhotoUploaded;
use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Photo;
use Illuminate\Http\Request;

class PhotoController extends Controller
{
    public function upload(Request $request, Album $album)
    {
        $this->authorize('update', $album);

        $validated = $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $uploadedCount = 0;
        $uploadedPhotos = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time().'_'.$image->hashName();
                $directory = 'uploads/album_photos/'.$album->id;

                $image->move(public_path($directory), $filename);
                $fullPath = $directory.'/'.$filename;

                $photo = Photo::create([
                    'album_id' => $album->id,
                    'user_id' => auth()->id(),
                    'title' => $request->input('title'),
                    'description' => $request->input('description'),
                    'image_path' => $fullPath,
                    'image_url' => asset($fullPath),
                    'mime_type' => $image->getMimeType(),
                    'file_size' => filesize(public_path($fullPath)),
                    'order' => Photo::where('album_id', $album->id)->max('order') + 1,
                ]);

                $uploadedPhotos[] = $photo;
                $uploadedCount++;

                // TRIGGER LARAVEL REVERB BROADCAST per foto (opsional, bisa digabung)
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
