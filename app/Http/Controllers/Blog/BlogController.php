<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return view('blog.index', compact('posts'));
    }

    public function show(Post $post)
    {
        if ($post->status !== 'published' && auth()->id() !== $post->user_id) {
            abort(404);
        }

        $post->incrementViews();
        $relatedPosts = Post::where('status', 'published')
            ->where('id', '!=', $post->id)
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }

    public function create()
    {
        return view('blog.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category' => 'required|string|max:50',
            'status' => 'required|in:draft,published',
        ]);

        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $filename = time().'_'.$file->hashName();
            $file->move(public_path('uploads/blogs'), $filename);
            $validated['featured_image'] = 'uploads/blogs/'.$filename;
        }

        $validated['user_id'] = auth()->id();
        $validated['published_at'] = $validated['status'] === 'published' ? now() : null;

        Post::create($validated);

        return redirect()->route('blog.index')->with('success', 'Post berhasil dibuat!');
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return view('blog.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category' => 'required|string|max:50',
            'status' => 'required|in:draft,published',
        ]);

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image && file_exists(public_path($post->featured_image))) {
                unlink(public_path($post->featured_image));
            }
            $file = $request->file('featured_image');
            $filename = time().'_'.$file->hashName();
            $file->move(public_path('uploads/blogs'), $filename);
            $validated['featured_image'] = 'uploads/blogs/'.$filename;
        }

        $validated['published_at'] = $validated['status'] === 'published' ? $post->published_at ?? now() : null;
        $post->update($validated);

        return redirect()->route('blog.index')->with('success', 'Post berhasil diperbarui!');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        if ($post->featured_image && file_exists(public_path($post->featured_image))) {
            unlink(public_path($post->featured_image));
        }

        $post->delete();

        return redirect()->route('blog.index')->with('success', 'Post berhasil dihapus!');
    }
}
