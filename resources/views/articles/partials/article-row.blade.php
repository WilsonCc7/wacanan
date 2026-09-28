@php
    $article = $article ?? null;
    $coverUrl = $article?->coverUrl() ?? '';
    $category = $article?->category ?? 'Story';
    $title = $article?->title ?? 'Untitled article';
    $excerpt = $article?->excerpt ?? '';
    $author = $article?->author_name ?? 'Wacanan Writer';
    $likes = (int) ($article?->likes_count ?? 0);
    $publishedLabel = $article?->published_at?->format('M j') ?? '';
@endphp

@if ($article?->exists)
    <article class="group grid grid-cols-1 gap-5 py-7 sm:grid-cols-[minmax(0,1fr)_9.5rem] sm:gap-6">
        <div class="min-w-0">
            <p class="text-xs font-bold uppercase tracking-[0.12em] text-accent-700">{{ $category }}</p>
            <h2 class="mt-2 text-lg font-bold leading-snug tracking-[-0.02em] text-neutral-950 sm:text-xl">
                <a href="{{ route('articles.show', $article->slug ?? '') }}" class="transition hover:text-accent-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-600 focus-visible:ring-offset-4">
                    {{ $title }}
                </a>
            </h2>

            @if ($excerpt !== '')
                <p class="mt-2 line-clamp-2 text-sm leading-6 text-neutral-600">{{ $excerpt }}</p>
            @endif

            <div class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-2 text-xs text-neutral-500">
                <x-avatar :name="$author" class="size-7 text-[10px]" />
                <span class="font-semibold text-neutral-800">{{ $author }}</span>
                @if ($publishedLabel !== '')
                    <span aria-hidden="true">•</span>
                    <time datetime="{{ $article?->published_at?->toIso8601String() ?? '' }}">{{ $publishedLabel }}</time>
                @endif
                <span aria-hidden="true">•</span>
                <span class="inline-flex items-center gap-1 tabular-nums">
                    <x-icon name="heart" class="size-3.5" />
                    {{ number_format($likes) }} likes
                </span>
            </div>
        </div>

        <a href="{{ route('articles.show', $article->slug ?? '') }}" class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-neutral-200 sm:aspect-square" aria-label="Read {{ $title }}">
            @if ($coverUrl !== '')
                <img src="{{ $coverUrl }}" alt="" loading="lazy" decoding="async"
                    class="h-full w-full object-cover transition duration-500 ease-out group-hover:scale-[1.03]">
            @else
                <span class="absolute inset-0 grid place-items-center bg-neutral-900 text-4xl font-black text-accent-300">
                    {{ Str::substr($category, 0, 1) }}
                </span>
            @endif
        </a>
    </article>
@endif
