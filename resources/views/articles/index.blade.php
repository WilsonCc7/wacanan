@extends('layouts.app')

@section('title', 'Articles | Wacanan')

@section('content')
    @php
        $articles = $articles ?? null;
        $categories = $categories ?? [];
        $query = $query ?? request()->query('search', '');
        $query = is_string($query) ? $query : '';
        $activeCategory = $activeCategory ?? request()->query('category', '');
        $activeCategory = is_string($activeCategory) ? $activeCategory : '';
        $baseQuery = $query !== '' ? ['search' => $query] : [];
        $articleCount = $articles?->total() ?? $articles?->count() ?? 0;
    @endphp

    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
        <header class="max-w-2xl">
            <h1 class="text-4xl font-black tracking-[-0.04em] text-neutral-950 sm:text-5xl">Find your next read.</h1>
            <p class="mt-4 text-base leading-7 text-neutral-600 sm:text-lg">Search the archive or follow a topic until something worth your time appears.</p>
        </header>

        <form action="{{ route('articles.index') }}" method="GET" class="relative mt-8" role="search">
            @if ($activeCategory !== '')
                <input type="hidden" name="category" value="{{ $activeCategory }}">
            @endif
            <label for="article-search" class="sr-only">Search articles</label>
            <x-icon name="search" class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-neutral-400" />
            <input id="article-search" type="search" name="search" value="{{ $query }}" placeholder="Search titles, summaries, or writers"
                class="w-full rounded-full border border-neutral-200 bg-white py-3.5 pl-12 pr-28 text-sm text-neutral-900 shadow-sm outline-none transition placeholder:text-neutral-400 focus:border-accent-500 focus:ring-4 focus:ring-accent-500/10 sm:pr-32">
            <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 rounded-full bg-neutral-950 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-accent-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-600 focus-visible:ring-offset-2 sm:px-5 sm:text-sm">
                Search
            </button>
        </form>

        <nav class="mt-6 flex gap-2 overflow-x-auto pb-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" aria-label="Article categories">
            <a href="{{ route('articles.index', $baseQuery) }}"
                class="shrink-0 rounded-full px-4 py-2 text-sm font-bold transition {{ $activeCategory === '' ? 'bg-accent-600 text-white' : 'border border-neutral-200 bg-white text-neutral-700 hover:border-accent-300 hover:text-accent-700' }}">
                All
            </a>
            @foreach ($categories as $category)
                <a href="{{ route('articles.index', array_merge($baseQuery, ['category' => $category])) }}"
                    class="shrink-0 rounded-full px-4 py-2 text-sm font-bold transition {{ $activeCategory === $category ? 'bg-accent-600 text-white' : 'border border-neutral-200 bg-white text-neutral-700 hover:border-accent-300 hover:text-accent-700' }}">
                    {{ $category }}
                </a>
            @endforeach
        </nav>

        <div class="mt-10 flex items-center justify-between border-b border-neutral-200 pb-4">
            <h2 class="text-lg font-black tracking-[-0.02em] text-neutral-950">
                @if ($activeCategory !== '')
                    {{ $activeCategory }}
                @else
                    All articles
                @endif
            </h2>
            <p class="text-sm tabular-nums text-neutral-500">{{ number_format((int) $articleCount) }} {{ Str::plural('story', (int) $articleCount) }}</p>
        </div>

        @if (($articles?->count() ?? 0) > 0)
            <div class="divide-y divide-neutral-200">
                @foreach ($articles as $article)
                    @include('articles.partials.article-row', ['article' => $article])
                @endforeach
            </div>

            @if ($articles?->hasPages() ?? false)
                <nav class="mt-10 border-t border-neutral-200 pt-8" aria-label="Article pages">
                    {{ $articles->links() }}
                </nav>
            @endif
        @else
            <div class="my-10 rounded-2xl border border-dashed border-neutral-300 bg-white px-6 py-16 text-center" role="status">
                <span class="mx-auto grid size-14 place-items-center rounded-full bg-accent-50 text-accent-700">
                    <x-icon name="document" class="size-6" />
                </span>
                @if ($query !== '' || $activeCategory !== '')
                    <h2 class="mt-5 text-xl font-black tracking-[-0.02em] text-neutral-950">No stories found here.</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-neutral-600">Try another keyword or return to all articles to keep exploring.</p>
                    <a href="{{ route('articles.index') }}" class="mt-6 inline-flex rounded-full bg-neutral-950 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-accent-700">Clear filters</a>
                @else
                    <h2 class="mt-5 text-xl font-black tracking-[-0.02em] text-neutral-950">No articles yet.</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-neutral-600">The first story could be yours. Start with an idea and give the community something worth reading.</p>
                    <a href="{{ auth()->check() ? route('admin.articles.create') : route('register') }}" class="mt-6 inline-flex rounded-full bg-accent-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-accent-700">Start writing</a>
                @endif
            </div>
        @endif
    </div>
@endsection
