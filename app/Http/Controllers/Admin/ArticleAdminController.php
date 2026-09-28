<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ArticleAdminController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');

        $query = Article::query()->latest('updated_at');

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('author_name', 'like', "%{$search}%");
            });
        }

        if ($status === 'published') {
            $query->whereNotNull('published_at');
        } elseif ($status === 'draft') {
            $query->whereNull('published_at');
        }

        return view('admin.articles.index', [
            'articles' => $query->paginate(12)->withQueryString(),
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('admin.articles.form', [
            'article' => null,
            'categories' => Article::CATEGORIES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title']);

        if ($request->hasFile('cover_image')) {
            // Covers live on Neon Object Storage when configured, else local public disk.
            $disk = config('filesystems.disks.covers.bucket') ? 'covers' : 'public';
            $data['cover_image'] = $request->file('cover_image')->store('covers', $disk);
            if ($disk !== 'public') {
                $data['cover_image'] = 'covers-disk:'.$data['cover_image'];
            }
        } else {
            unset($data['cover_image']);
        }

        $data['published_at'] = $this->publishedAt($request, $data);
        unset($data['status']);
        $data['author_name'] = $request->user()?->name;
        $data['user_id'] = $request->user()?->id;

        Article::create($data);

        return redirect()
            ->route('admin.articles.index')
            ->with('status', 'Article created successfully.');
    }

    public function edit(Article $article): View
    {
        return view('admin.articles.form', [
            'article' => $article,
            'categories' => Article::CATEGORIES,
        ]);
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $data = $request->validate($this->rules($article));
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title'], $article);

        if ($request->hasFile('cover_image')) {
            $oldCover = $article->cover_image;
            $disk = config('filesystems.disks.covers.bucket') ? 'covers' : 'public';
            $data['cover_image'] = $request->file('cover_image')->store('covers', $disk);
            if ($disk !== 'public') {
                $data['cover_image'] = 'covers-disk:'.$data['cover_image'];
            }

            $this->deleteCoverFile($oldCover);
        } else {
            unset($data['cover_image']);
        }

        $data['published_at'] = $this->publishedAt($request, $data, $article);
        unset($data['status']);

        $article->update($data);

        return redirect()
            ->route('admin.articles.index')
            ->with('status', 'Article updated successfully.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $this->deleteCoverFile($article->cover_image);

        $article->delete();

        return redirect()
            ->route('admin.articles.index')
            ->with('status', 'Article deleted successfully.');
    }

    /**
     * Deletes an uploaded cover from whichever disk holds it.
     * Seeded rows keep remote http URLs; uploads are local paths or covers-disk: prefixed.
     */
    private function deleteCoverFile(?string $cover): void
    {
        if (! $cover || str_starts_with($cover, 'http')) {
            return;
        }

        if (str_starts_with($cover, 'covers-disk:')) {
            Storage::disk('covers')->delete(substr($cover, strlen('covers-disk:')));

            return;
        }

        Storage::disk('public')->delete($cover);
    }

    public function bulk(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', Rule::in(['publish', 'delete'])],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $articles = Article::whereKey($data['ids']);

        if ($data['action'] === 'delete') {
            $count = $articles->delete();
            $message = "{$count} article(s) deleted successfully.";
        } else {
            $count = $articles->whereNull('published_at')->update(['published_at' => now()]);
            $message = "{$count} article(s) published successfully.";
        }

        return redirect()
            ->route('admin.articles.index')
            ->with('status', $message);
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function rules(?Article $article = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('articles', 'slug')->ignore($article?->getKey()),
            ],
            'excerpt' => ['nullable', 'string', 'max:160'],
            'body' => ['required', 'string', 'min:20'],
            'category' => ['required', 'string', 'max:255'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'status' => ['nullable', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
        ];
    }

    private function uniqueSlug(?string $slug, string $title, ?Article $article = null): string
    {
        $base = Str::slug($slug ?: $title) ?: Str::random(8);
        $candidate = $base;
        $suffix = 2;

        while (Article::query()
            ->when($article, fn (Builder $query) => $query->where('id', '!=', $article->getKey()))
            ->where('slug', $candidate)
            ->exists()) {
            $candidate = $base.'-'.$suffix++;
        }

        return $candidate;
    }

    private function publishedAt(Request $request, array $data, ?Article $article = null): \DateTimeInterface|string|null
    {
        $status = $request->input('status');

        if ($status === 'draft') {
            return null;
        }

        if ($status === 'published') {
            return ! empty($data['published_at']) ? $data['published_at'] : now();
        }

        return $article?->published_at;
    }
}
