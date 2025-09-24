<?php

namespace App\Http\Controllers;
use App\Models\CommunityPost;

use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function index()
    {
        $posts = CommunityPost::latest()->get()->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'description' => $post->description,
                'images' => json_decode($post->images),
                'author' => [
                    'name' => $post->author_name,
                    'avatar' => $post->author_avatar,
                    'username' => $post->author_username,
                ],
                'tags' => json_decode($post->tags),
                'likes' => 0,
                'isLiked' => false,
                'comments' => [],
                'showComments' => false,
                'timeAgo' => $post->created_at->diffForHumans()
            ];
        });

        return response()->json(['posts' => $posts]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'images' => 'required|array',
            'author_name' => 'required|string',
            'author_avatar' => 'nullable|string',
            'author_username' => 'nullable|string',
            'tags' => 'nullable|array'
        ]);

        $post = CommunityPost::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'images' => json_encode($validated['images']),
            'author_name' => $validated['author_name'],
            'author_avatar' => $validated['author_avatar'] ?? null,
            'author_username' => $validated['author_username'] ?? null,
            'tags' => json_encode($validated['tags'] ?? [])
        ]);

        return response()->json(['message' => 'Post created successfully']);
    }
}

