<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(): Response
    {
        $posts = Post::published()
            ->with(['author:id,name', 'category:id,name,slug'])
            ->select(['id', 'title', 'slug', 'excerpt', 'featured_image', 'published_at', 'author_id', 'category_id'])
            ->latest('published_at')
            ->paginate(9);

        $categories = PostCategory::withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return Inertia::render('Blog/Index', [
            'posts'      => $posts,
            'categories' => $categories,
        ]);
    }

    public function show(string $category, string $slug): Response
    {
        $cat  = PostCategory::where('slug', $category)->firstOrFail();

        $post = Post::published()
            ->with(['author:id,name', 'category:id,name,slug'])
            ->where('category_id', $cat->id)
            ->where('slug', $slug)
            ->firstOrFail();

        return Inertia::render('Blog/Show', [
            'post' => $post,
        ]);
    }

    /** Legacy: posts created before categories were introduced, or posts with no category. */
    public function showLegacy(string $slug): RedirectResponse|Response
    {
        $post = Post::published()
            ->with(['author:id,name', 'category:id,name,slug'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Redirect to canonical URL if post has a category
        if ($post->category) {
            return redirect()->route('blog.show', [$post->category->slug, $post->slug], 301);
        }

        return Inertia::render('Blog/Show', [
            'post' => $post,
        ]);
    }

    public function category(string $category): Response
    {
        $cat = PostCategory::where('slug', $category)->firstOrFail();

        $posts = Post::published()
            ->with(['author:id,name', 'category:id,name,slug'])
            ->select(['id', 'title', 'slug', 'excerpt', 'featured_image', 'published_at', 'author_id', 'category_id'])
            ->where('category_id', $cat->id)
            ->latest('published_at')
            ->paginate(9);

        $categories = PostCategory::withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return Inertia::render('Blog/Index', [
            'posts'           => $posts,
            'categories'      => $categories,
            'activeCategory'  => $cat,
        ]);
    }
}
