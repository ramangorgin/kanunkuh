<?php

/**
 * Admin endpoints for managing blog posts and related media.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Provides CRUD, publish, and image upload actions for posts.
 */
class PostController extends Controller
{
    /**
     * Enforce admin authentication for post management.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * List posts with author and category relations.
     */
    public function index()
    {
        $posts = Post::with(['categories', 'user'])->latest()->paginate(20);

        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the post creation form.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.posts.create', compact('categories'));
    }

    /**
     * Persist a new post and sync selected categories.
     */
    public function store(PostStoreRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['is_indexable'] = $request->boolean('is_indexable', true);
        $data['is_followable'] = $request->boolean('is_followable', true);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('uploads/posts', 'public');
        }

        if (($data['status'] ?? 'draft') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post = Post::create($data);
        $post->categories()->sync($request->input('categories', []));

        return redirect()->route('admin.posts.index')->with('success', 'پست با موفقیت ایجاد شد.');
    }

    /**
     * Show the edit form for a post with its categories.
     */
    public function edit(Post $post)
    {
        $categories = Category::orderBy('name')->get();
        $post->load('categories');

        return view('admin.posts.edit', compact('post', 'categories'));
    }

    /**
     * Update a post and sync its categories.
     */
    public function update(PostUpdateRequest $request, Post $post)
    {
        $data = $request->validated();
        $data['is_indexable'] = $request->boolean('is_indexable', true);
        $data['is_followable'] = $request->boolean('is_followable', true);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('uploads/posts', 'public');
        }

        if (($data['status'] ?? 'draft') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post->update($data);
        $post->categories()->sync($request->input('categories', []));

        return redirect()->route('admin.posts.index')->with('success', 'پست بروزرسانی شد.');
    }

    /**
     * Delete a post and detach its categories.
     */
    public function destroy(Post $post)
    {
        $post->categories()->detach();
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'پست حذف شد.');
    }

    /**
     * Publish a post and ensure a publish timestamp is set.
     */
    public function publish(Post $post)
    {
        $post->status = 'published';
        if (!$post->published_at) {
            $post->published_at = now();
        }
        $post->save();

        return redirect()->route('admin.posts.index')->with('success', 'پست منتشر شد.');
    }

    /**
     * Upload a post image for rich-text editors.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'upload' => ['required', 'file', 'image', 'max:4096'],
            'alt' => ['required', 'string', 'max:255'],
        ]);

        if (!$request->hasFile('upload') || !$request->file('upload')->isValid()) {
            return response()->json(['uploaded' => 0, 'error' => ['message' => 'تصویر معتبر نیست.']], 422);
        }

        $path = $request->file('upload')->store('uploads/posts', 'public');

        return response()->json([
            'uploaded' => 1,
            'fileName' => basename($path),
            'url' => asset('storage/'.$path),
            'alt' => $request->input('alt'),
        ]);
    }
}
