<?php

namespace App\Http\Controllers\Album;

use App\Events\AlbumCreated;
use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function index()
    {
        $albums = Album::where('status', 'published')
            ->with('photos')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $albums,
        ]);
    }

    public function show(Album $album)
    {
        if ($album->status !== 'published' && auth()->id() !== $album->user_id) {
            return response()->json(['success' => false, 'message' => 'Album tidak ditemukan'], 404);
        }

        $album->load('photos');

        return response()->json([
            'success' => true,
            'data' => $album,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time().'_'.$file->hashName();
            $file->move(public_path('uploads/albums'), $filename);
            $validated['thumbnail'] = 'uploads/albums/'.$filename;
        }

        $validated['user_id'] = auth()->id();
        $validated['published_at'] = $validated['status'] === 'published' ? now() : null;

        $album = Album::create($validated);

        // TRIGGER LARAVEL REVERB BROADCAST
        broadcast(new AlbumCreated($album))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Album berhasil dibuat!',
            'data' => $album,
        ], 201);
    }

    public function update(Request $request, Album $album)
    {
        // Pastikan logic authorization lu (Policy) support exception response
        $this->authorize('update', $album);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($album->thumbnail && file_exists(public_path($album->thumbnail))) {
                unlink(public_path($album->thumbnail));
            }
            $file = $request->file('thumbnail');
            $filename = time().'_'.$file->hashName();
            $file->move(public_path('uploads/albums'), $filename);
            $validated['thumbnail'] = 'uploads/albums/'.$filename;
        }

        $validated['published_at'] = $validated['status'] === 'published' ? $album->published_at ?? now() : null;
        $album->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Album berhasil diperbarui!',
            'data' => $album,
        ]);
    }

    public function destroy(Album $album)
    {
        $this->authorize('delete', $album);

        if ($album->thumbnail && file_exists(public_path($album->thumbnail))) {
            unlink(public_path($album->thumbnail));
        }

        $album->delete();

        return response()->json([
            'success' => true,
            'message' => 'Album berhasil dihapus!',
        ]);
    }
}
