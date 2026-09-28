@extends('layouts.app')

@section('title', 'Wacanan | Stories worth your attention')

@section('content')
    @php
        $featured = $featured ?? null;
        $articles = $articles ?? collect();
        $categories = $categories ?? [];
        $latestArticles = $articles
            ->reject(fn ($article) => $featured?->exists && $article?->getKey() === $featured->getKey())
            ->values();
        $topWriters = $articles
            ->filter(fn ($article) => filled($article?->author_name ?? null))
            ->groupBy(fn ($article) => $article?->author_name ?? '')
            ->map(function ($items) {
                $first = $items->first();

                return [
                    'name' => $first?->author_name ?? 'Wacanan Writer',
                    'likes' => (int) ($items->sum(fn ($article) => (int) ($article?->likes_count ?? 0))),
                ];
            })
            ->sortByDesc('likes')
            ->take(4)
            ->values();
        $readingList = $articles->take(3)->values();
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <section class="relative grid overflow-hidden rounded-2xl bg-neutral-950 text-white md:grid-cols-[1.15fr_0.85fr]" aria-labelledby="home-hero-title">
            <div class="relative flex flex-col justify-center px-6 py-10 sm:px-10 sm:py-14 lg:px-14">
                <canvas data-hero-canvas aria-hidden="true" class="pointer-events-none absolute inset-0 h-full w-full opacity-90"></canvas>
                <h1 id="home-hero-title" class="relative max-w-2xl font-display text-4xl font-black leading-[0.98] tracking-[-0.04em] sm:text-5xl lg:text-6xl">
                    Stories for the endlessly curious.
                </h1>
                <p class="relative mt-5 max-w-xl text-base leading-7 text-neutral-300 sm:text-lg">
                    Find thoughtful writing on design, technology, business, culture, and the ideas moving between them.
                </p>
                <div class="relative mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('articles.index') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-bold text-neutral-950 transition hover:bg-accent-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-400 focus-visible:ring-offset-2 focus-visible:ring-offset-neutral-950">
                        Find Article
                        <x-icon name="arrow-right" class="size-4" />
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full border border-neutral-700 px-6 py-3 text-sm font-bold text-white transition hover:border-accent-400 hover:text-accent-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-400 focus-visible:ring-offset-2 focus-visible:ring-offset-neutral-950">
                        Start writing
                    </a>
                </div>
            </div>

            <div class="relative min-h-64 overflow-hidden border-t border-white/10 md:min-h-full md:border-l md:border-t-0">
                @if (filled($featured?->coverUrl() ?? ''))
                    <img src="{{ $featured?->coverUrl() ?? '' }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-65">
                @endif
                <div class="absolute inset-0 bg-neutral-950/30"></div>
                <div class="absolute inset-x-5 bottom-5 rounded-2xl border border-white/15 bg-neutral-950/80 p-5 backdrop-blur-sm sm:inset-x-7 sm:bottom-7 sm:p-6">
                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-accent-300">Editor’s pick</p>
                    <p class="mt-2 line-clamp-2 text-lg font-bold leading-snug text-white">
                        {{ $featured?->title ?? 'A thoughtful read is waiting for you.' }}
                    </p>
                </div>
            </div>
        </section>

        <section class="py-14 sm:py-20" aria-labelledby="featured-title" data-reveal>
            <div class="mb-7 flex items-end justify-between gap-4">
                <div>
                    <h2 id="featured-title" class="text-2xl font-black tracking-[-0.03em] text-neutral-950 sm:text-3xl">Featured today</h2>
                    <p class="mt-2 text-sm text-neutral-600">One considered read to start your day.</p>
                </div>
                <a href="{{ route('articles.index') }}" class="hidden items-center gap-2 text-sm font-bold text-accent-700 transition hover:text-accent-900 sm:inline-flex">
                    Browse all
                    <x-icon name="arrow-right" class="size-4" />
                </a>
            </div>

            @if ($featured?->exists)
                @php
                    $featuredCover = $featured?->coverUrl() ?? '';
                    $featuredAuthor = $featured?->author_name ?? 'Wacanan Writer';
                @endphp
                <article class="grid overflow-hidden rounded-2xl border border-neutral-200 bg-white transition duration-500 hover:-translate-y-1 hover:shadow-xl hover:shadow-neutral-900/10 md:grid-cols-2" data-reveal>
                    <a href="{{ route('articles.show', $featured?->slug ?? '') }}" class="relative min-h-72 overflow-hidden bg-neutral-200 md:min-h-[27rem]" aria-label="Read {{ $featured?->title ?? 'featured article' }}">
                        @if ($featuredCover !== '')
                            <img src="{{ $featuredCover }}" alt="" class="absolute inset-0 h-full w-full object-cover transition duration-500 hover:scale-[1.02]">
                        @else
                            <span class="absolute inset-0 grid place-items-center bg-neutral-900 text-7xl font-black text-accent-300">
                                {{ Str::substr($featured?->category ?? 'W', 0, 1) }}
                            </span>
                        @endif
                    </a>
                    <div class="flex flex-col justify-center p-6 sm:p-9 lg:p-12">
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-accent-700">{{ $featured?->category ?? 'Story' }}</p>
                        <h3 class="mt-3 text-3xl font-black leading-tight tracking-[-0.035em] text-neutral-950 sm:text-4xl">
                            <a href="{{ route('articles.show', $featured?->slug ?? '') }}" class="transition hover:text-accent-700">
                                {{ $featured?->title ?? 'Untitled article' }}
                            </a>
                        </h3>
                        @if (filled($featured?->excerpt ?? ''))
                            <p class="mt-4 text-base leading-7 text-neutral-600">{{ $featured?->excerpt ?? '' }}</p>
                        @endif
                        <div class="mt-6 flex flex-wrap items-center gap-3 text-sm text-neutral-500">
                            <x-avatar :name="$featuredAuthor" class="size-9 text-xs" />
                            <span class="font-semibold text-neutral-800">{{ $featuredAuthor }}</span>
                            <span aria-hidden="true">•</span>
                            <span class="inline-flex items-center gap-1.5 tabular-nums">
                                <x-icon name="heart" class="size-4" />
                                {{ number_format((int) ($featured?->likes_count ?? 0)) }} likes
                            </span>
                        </div>
                        <a href="{{ route('articles.show', $featured?->slug ?? '') }}" class="mt-8 inline-flex w-fit items-center gap-2 rounded-full bg-accent-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-accent-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-600 focus-visible:ring-offset-2">
                            Read the story
                            <x-icon name="arrow-right" class="size-4" />
                        </a>
                    </div>
                </article>
            @else
                <div class="rounded-2xl border border-dashed border-neutral-300 bg-white px-6 py-12 text-center">
                    <h3 class="text-lg font-bold text-neutral-900">The first story is still being written.</h3>
                    <a href="{{ route('register') }}" class="mt-4 inline-flex rounded-full bg-accent-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-accent-700">Start writing</a>
                </div>
            @endif
        </section>

        <div class="grid gap-12 border-t border-neutral-200 pb-16 pt-12 md:grid-cols-[minmax(0,1fr)_19rem] md:gap-10 lg:gap-14">
            <section aria-labelledby="latest-title" data-reveal>
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2 id="latest-title" class="text-2xl font-black tracking-[-0.03em] text-neutral-950 sm:text-3xl">Latest stories</h2>
                        <p class="mt-2 text-sm text-neutral-600">Fresh ideas from the Wacanan community.</p>
                    </div>
                    <span class="text-xs font-semibold tabular-nums text-neutral-500">{{ $articles->count() }} reads</span>
                </div>

                <div class="mt-3 divide-y divide-neutral-200">
                    @forelse ($latestArticles as $article)
                        @include('articles.partials.article-row', ['article' => $article])
                    @empty
                        <p class="py-10 text-sm text-neutral-500">No other stories yet.</p>
                    @endforelse
                </div>
            </section>

            <aside class="space-y-6" aria-label="Wacanan discovery">
                <section class="rounded-2xl border border-neutral-200 bg-white p-5" aria-labelledby="writers-title">
                    <div class="flex items-center gap-3">
                        <span class="grid size-9 place-items-center rounded-full bg-accent-50 text-accent-700">
                            <x-icon name="users" class="size-4.5" />
                        </span>
                        <h2 id="writers-title" class="font-bold text-neutral-950">Top Writers</h2>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse ($topWriters as $writer)
                            <div class="flex items-center gap-3">
                                <x-avatar :name="$writer['name'] ?? 'Wacanan Writer'" class="size-9 text-xs" />
                                <p class="min-w-0 flex-1 truncate text-sm font-semibold text-neutral-800">{{ $writer['name'] ?? 'Wacanan Writer' }}</p>
                                <button type="button" data-follow class="text-xs font-bold text-neutral-700 transition hover:text-accent-700" aria-label="Follow {{ $writer['name'] ?? 'writer' }}">
                                    <span data-follow-off>Follow</span>
                                    <span data-follow-on class="hidden text-accent-700">Following</span>
                                </button>
                            </div>
                        @empty
                            <p class="text-sm text-neutral-500">Writers will appear here soon.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-2xl border border-neutral-200 bg-white p-5" aria-labelledby="topics-title">
                    <div class="flex items-center gap-3">
                        <span class="grid size-9 place-items-center rounded-full bg-accent-50 text-accent-700">
                            <x-icon name="sparkles" class="size-4.5" />
                        </span>
                        <h2 id="topics-title" class="font-bold text-neutral-950">Topics for you</h2>
                    </div>
                    <div class="mt-5 flex flex-wrap gap-2">
                        @foreach ($categories as $category)
                            <a href="{{ route('articles.index', ['category' => $category]) }}" class="rounded-full border border-neutral-200 px-3.5 py-2 text-xs font-semibold text-neutral-700 transition hover:border-accent-300 hover:bg-accent-50 hover:text-accent-800">
                                {{ $category }}
                            </a>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-2xl border border-neutral-200 bg-white p-5" aria-labelledby="reading-list-title">
                    <div class="flex items-center gap-3">
                        <span class="grid size-9 place-items-center rounded-full bg-accent-50 text-accent-700">
                            <x-icon name="bookmark" class="size-4.5" />
                        </span>
                        <h2 id="reading-list-title" class="font-bold text-neutral-950">Reading List</h2>
                    </div>

                    <div class="mt-4 divide-y divide-neutral-100">
                        @forelse ($readingList as $savedArticle)
                            @php
                                $savedCover = $savedArticle?->coverUrl() ?? '';
                            @endphp
                            <a href="{{ route('articles.show', $savedArticle?->slug ?? '') }}" class="group grid grid-cols-[minmax(0,1fr)_3.5rem] gap-3 py-3 first:pt-1 last:pb-0">
                                <span class="min-w-0">
                                    <span class="block text-[11px] font-bold uppercase tracking-[0.1em] text-accent-700">{{ $savedArticle?->category ?? 'Story' }}</span>
                                    <span class="mt-1 line-clamp-2 text-sm font-semibold leading-5 text-neutral-800 transition group-hover:text-accent-700">{{ $savedArticle?->title ?? 'Untitled article' }}</span>
                                </span>
                                <span class="relative aspect-square overflow-hidden rounded-xl bg-neutral-200">
                                    @if ($savedCover !== '')
                                        <img src="{{ $savedCover }}" alt="" loading="lazy" class="h-full w-full object-cover">
                                    @else
                                        <span class="absolute inset-0 grid place-items-center bg-neutral-900 text-sm font-black text-accent-300">{{ Str::substr($savedArticle?->category ?? 'W', 0, 1) }}</span>
                                    @endif
                                </span>
                            </a>
                        @empty
                            <p class="py-3 text-sm text-neutral-500">Your next read is one click away.</p>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </div>
@endsection
