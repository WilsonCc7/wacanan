<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Toggle a session-scoped like and report the new totals.
     */
    public function toggle(Request $request, Article $article): JsonResponse
    {
        $key = 'liked_'.$article->id;

        if ($request->session()->has($key)) {
            $request->session()->forget($key);
            $article->decrement('likes_count');
            $liked = false;
        } else {
            $request->session()->put($key, true);
            $article->increment('likes_count');
            $liked = true;
        }

        $article->refresh();

        return response()->json([
            'likes' => max(0, (int) $article->likes_count),
            'liked' => $liked,
        ]);
    }

    public function __invoke(Request $request, Article $article): JsonResponse
    {
        return $this->toggle($request, $article);
    }
}
