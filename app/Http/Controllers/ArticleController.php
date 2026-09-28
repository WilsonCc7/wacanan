<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Published article listing with search and category filtering.
     */
    public function index(Request $request): View
    {
        $query = Article::published()->latest('published_at');

        $search = $request->query('search');
        $activeCategory = $request->query('category');

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('author_name', 'like', "%{$search}%");
            });
        }

        if ($activeCategory) {
            $query->where('category', $activeCategory);
        }

        return view('articles.index', [
            'articles' => $query->paginate(8)->withQueryString(),
            'categories' => Article::CATEGORIES,
            'activeCategory' => $activeCategory,
            'query' => $search,
        ]);
    }

    /**
     * Single article read page with three related picks.
     */
    public function show(Request $request, Article $article): View
    {
        abort_unless($article->published_at?->isPast(), 404);

        $related = Article::published()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->latest('published_at')
            ->limit(3)
            ->get();

        // Top up from other categories when the same category is thin.
        if ($related->count() < 3) {
            $related = $related->concat(
                Article::published()
                    ->where('id', '!=', $article->id)
                    ->whereNotIn('id', $related->pluck('id'))
                    ->inRandomOrder()
                    ->limit(3 - $related->count())
                    ->get()
            );
        }

        return view('articles.show', [
            'article' => $article,
            'related' => $related,
            'liked' => $request->session()->has('liked_'.$article->id),
        ]);
    }
}
