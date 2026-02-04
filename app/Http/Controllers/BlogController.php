<?php

/**
 * Public blog endpoints for listing and displaying posts.
 */

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

/**
 * Handles retrieval of published posts and single-post views.
 */
class BlogController extends Controller
{
    public function index()
    {
        /**
         * Return a paginated list of published blog posts.
         */
        $posts = Post::with(['categories', 'user'])
            ->published()
            ->latest('published_at')
            ->paginate(9);

        return view('blog.index', compact('posts'));
    }

    public function show(string $slug)
    {
        /**
         * Show a single published post by slug and increment view count.
         */
        $post = Post::published()->where('slug', $slug)->firstOrFail();
        $post->increment('view_count');

        return view('blog.show', compact('post'));
    }
}
