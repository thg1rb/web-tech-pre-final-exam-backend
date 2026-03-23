<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Storage;
use Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with('user')->latest()->get();

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        // Validate and return every fields in `rules` method
        $data = $request->validated();

        // Validate if `image` file exist
        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', [
                'disk' => 's3',
                'visibility' => 'public',
            ]);
        }

        // Create new post in database
        $post = Post::create([
            'id' => Str::uuid(),
            'user_id' => auth()->id(),
            'title' => $data['title'],
            'content' => $data['content'],
            'image_path' => $path,
        ]);

        // Remove cache
        Cache::forget(Post::CACHE_KEY_RECOMMENDED);

        // Response
        return response()->json([
            'message' => 'Post created successfully',
            'data' => $post,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load('user');

        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        Gate::authorize('update', $post);

        // Validate and return every fields in `rules` method
        $data = $request->validated();

        // New image
        if ($request->hasFile('image')) {
            // Delete old image
            if ($post->image_path) {
                Storage::disk('s3')->delete($post->image_path);
            }

            // Replace with new image
            $data['image_path'] = $request->file('image')->store('posts', 's3');
        }

        $post->update(collect($data)->except('image')->toArray());

        return response()->json([
            'message' => 'Post updated successfully',
            'data' => $post,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        // Authorization check; if not owner return 403
        Gate::authorize('delete', $post);

        $post->delete();

        // Remove cache
        Cache::forget(Post::CACHE_KEY_RECOMMENDED);

        return response()->json([
            'message' => 'Post deleted successfully',
        ], 200);
    }

    public function recommended()
    {
        $posts = Cache::remember(Post::CACHE_KEY_RECOMMENDED, 60 * 60 * 24, function () {
            return Post::query()->with('user')->inRandomOrder()->get()->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'author_name' => $post->user->username,
                    'image_url' => $post->image_path ? Storage::disk('s3')->url($post->image_path) : null,
                    'posted_at' => $post->created_at->diffForHumans(),
                ];
            })->toArray(); // แปลงเป็น Array ธรรมดาเพื่อเก็บลง Cache
        });

        return response()->json([
            'data' => $posts
        ]);
    }
}
