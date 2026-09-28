<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $articles = Article::published()->latest('published_at')->take(10)->get();

        return view('home', [
            'featured' => $articles->first(),
            'articles' => $articles,
            'categories' => Article::CATEGORIES,
        ]);
    }

    public function __invoke(): View
    {
        return $this->index();
    }
}
