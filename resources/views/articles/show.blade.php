@extends('layouts.app')

@section('title', ($article?->title ?? 'Article').' | Wacanan')

@section('content')
    @php
        $article = $article ?? null;
        $related = $related ?? collect();
        $liked = $liked ?? false;
        $articleUrl = $article?->exists ? route('articles.show', $article->slug ?? '') : '';
        $likeUrl = $article?->exists ? route('articles.like', $article) : '';
        $title = $article?->title ?? 'Untitled article';
        $author = $article?->author_name ?? 'Wacanan Writer';
        $category = $article?->category ?? 'Story';
        $excerpt = $article?->excerpt ?? '';
        $coverUrl = $article?->coverUrl() ?? '';
        $encodedUrl = rawurlencode($articleUrl);
        $encodedTitle = rawurlencode($title);
        $body = trim($article?->body ?? '');
        $paragraphs = $body !== '' ? (preg_split('/\R{2,}/', $body) ?? []) : [];
        $paragraphs = array_values(array_filter(array_map('trim', $paragraphs)));
        $pullQuoteSource = $body !== '' ? $body : $excerpt;
        $pullQuote = $pullQuoteSource !== '' ? Str::limit(strip_tags($pullQuoteSource), 140) : '';
        $sections = collect($paragraphs)
            ->map(function ($paragraph, $index) {
                $plainText = strip_tags($paragraph);

                return [
                    'id' => 'section-'.($index + 1),
                    'label' => Str::limit($plainText !== '' ? $plainText : 'Story section', 48),
                ];
            })
            ->values();
        $tags = collect([$category])->filter()->unique()->values();
    @endphp

    @if ($article?->exists)
        <article>
            <header class="relative isolate overflow-hidden bg-neutral-950 text-white">
                @if ($coverUrl !== '')
                    <img src="{{ $coverUrl }}" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover opacity-35">
                @endif
                <div class="absolute inset-0 -z-10 bg-neutral-950/65"></div>

                <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8 lg:py-16">
                    <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-neutral-300 transition hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-400 focus-visible:ring-offset-2 focus-visible:ring-offset-neutral-950">
                        <x-icon name="arrow-left" class="size-4" />
                        Back to all articles
                    </a>

                    <div class="mt-10 max-w-4xl sm:mt-12">
                        <h1 class="font-serif text-4xl font-medium leading-[1.06] tracking-[-0.025em] text-white sm:text-5xl lg:text-6xl">{{ $title }}</h1>
                        @if ($excerpt !== '')
                            <p class="mt-6 max-w-3xl text-base leading-7 text-neutral-200 sm:text-lg sm:leading-8">{{ $excerpt }}</p>
                        @endif

                        <div class="mt-8 flex flex-col gap-5 border-t border-white/15 pt-6 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <x-avatar :name="$author" class="size-11 text-sm ring-white/20" />
                                <div>
                                    <p class="font-bold text-white">{{ $author }}</p>
                                    <p class="mt-0.5 text-xs text-neutral-400">
                                        {{ $article?->published_at?->format('F j, Y') ?? 'Recently published' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-2" aria-label="Article actions">
                                <button type="button" data-copy-url="{{ $articleUrl }}" data-copy-label data-original="Copy link"
                                    class="inline-flex items-center gap-2 rounded-full border border-white/20 px-3.5 py-2 text-xs font-bold text-white transition hover:border-accent-400 hover:text-accent-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-400">
                                    <x-icon name="link" class="size-4" />
                                    <span data-copy-label>Copy link</span>
                                </button>
                                <a href="https://x.com/intent/post?url={{ $encodedUrl }}&text={{ $encodedTitle }}" target="_blank" rel="noopener noreferrer"
                                    class="grid size-9 place-items-center rounded-full border border-white/20 text-white transition hover:border-accent-400 hover:text-accent-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-400" aria-label="Share on X">
                                    <x-icon name="x" class="size-3.5" />
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}" target="_blank" rel="noopener noreferrer"
                                    class="grid size-9 place-items-center rounded-full border border-white/20 text-white transition hover:border-accent-400 hover:text-accent-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-400" aria-label="Share on Facebook">
                                    <x-icon name="facebook" class="size-4" />
                                </a>
                                <a href="https://wa.me/?text={{ $encodedTitle }}%20{{ $encodedUrl }}" target="_blank" rel="noopener noreferrer"
                                    class="grid size-9 place-items-center rounded-full border border-white/20 text-white transition hover:border-accent-400 hover:text-accent-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-400" aria-label="Share on WhatsApp">
                                    <x-icon name="whatsapp" class="size-4" />
                                </a>
                                <button type="button" data-like-button data-like-url="{{ $likeUrl }}" aria-pressed="{{ $liked ? 'true' : 'false' }}"
                                    class="like-btn-dark inline-flex items-center gap-2 rounded-full border border-white/20 px-3.5 py-2 text-xs font-bold text-white transition hover:border-accent-400 disabled:cursor-wait disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-400">
                                    <x-icon name="heart" class="size-4" />
                                    <span data-like-count>{{ number_format((int) ($article?->likes_count ?? 0)) }}</span>
                                    <span class="sr-only">likes</span>
                                </button>
                            </div>
                        </div>
                        <p data-like-status class="mt-2 min-h-5 text-xs text-red-300" role="status" aria-live="polite"></p>

                        @if ($tags->isNotEmpty())
                            <div class="mt-5 flex flex-wrap gap-2" aria-label="Article topics">
                                @foreach ($tags as $tag)
                                    <a href="{{ route('articles.index', ['category' => $tag]) }}" class="rounded-full border border-white/20 px-3 py-1.5 text-xs font-semibold text-neutral-200 transition hover:border-accent-400 hover:text-accent-300">
                                        {{ $tag }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </header>

            <div class="bg-white">
                <div class="mx-auto grid max-w-6xl gap-10 px-4 py-12 sm:px-6 sm:py-16 md:grid-cols-[13rem_minmax(0,1fr)] md:gap-12 lg:px-8 lg:py-20">
                    <aside class="md:sticky md:top-24 md:self-start" aria-labelledby="toc-title">
                        <h2 id="toc-title" class="text-xs font-black uppercase tracking-[0.15em] text-neutral-500">Table of Contents</h2>
                        @if ($sections->isNotEmpty())
                            <ol class="mt-4 space-y-1.5 border-l border-neutral-200">
                                @foreach ($sections as $section)
                                    <li>
                                        <a href="#{{ $section['id'] ?? 'section' }}" class="-ml-px block border-l border-transparent py-1.5 pl-4 text-sm leading-5 text-neutral-600 transition hover:border-accent-500 hover:text-neutral-950">
                                            {{ $section['label'] ?? 'Story section' }}
                                        </a>
                                    </li>
                                @endforeach
                            </ol>
                        @else
                            <p class="mt-3 text-sm text-neutral-500">This story is just getting started.</p>
                        @endif
                    </aside>

                    <div class="min-w-0">
                        <div class="mx-auto max-w-3xl">
                            @if ($pullQuote !== '')
                                <blockquote class="mb-10 rounded-2xl bg-neutral-950 p-6 font-serif text-2xl leading-snug text-white sm:mb-12 sm:p-8 sm:text-3xl">
                                    “{{ $pullQuote }}”
                                </blockquote>
                            @endif

                            <div class="prose-article max-w-[68ch]">
                                @forelse ($paragraphs as $index => $paragraph)
                                    <p id="section-{{ $index + 1 }}">{{ $paragraph }}</p>
                                @empty
                                    <p>{{ $excerpt }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($related->isNotEmpty())
                <section class="border-t border-neutral-200 bg-neutral-50" aria-labelledby="related-title">
                    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
                        <div class="flex items-end justify-between gap-4">
                            <div>
                                <h2 id="related-title" class="text-2xl font-black tracking-[-0.03em] text-neutral-950 sm:text-3xl">Keep reading</h2>
                                <p class="mt-2 text-sm text-neutral-600">More ideas selected for you.</p>
                            </div>
                            <a href="{{ route('articles.index') }}" class="hidden items-center gap-2 text-sm font-bold text-accent-700 transition hover:text-accent-900 sm:inline-flex">
                                All articles
                                <x-icon name="arrow-right" class="size-4" />
                            </a>
                        </div>

                        <div class="mt-7 grid gap-5 md:grid-cols-3">
                            @foreach ($related->take(3) as $relatedArticle)
                                @php
                                    $relatedCover = $relatedArticle?->coverUrl() ?? '';
                                    $relatedTitle = $relatedArticle?->title ?? 'Untitled article';
                                @endphp
                                <article class="group overflow-hidden rounded-2xl border border-neutral-200 bg-white">
                                    <a href="{{ route('articles.show', $relatedArticle?->slug ?? '') }}" class="relative block aspect-[16/10] overflow-hidden bg-neutral-200" aria-label="Read {{ $relatedTitle }}">
                                        @if ($relatedCover !== '')
                                            <img src="{{ $relatedCover }}" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
                                        @else
                                            <span class="absolute inset-0 grid place-items-center bg-neutral-900 text-5xl font-black text-accent-300">{{ Str::substr($relatedArticle?->category ?? 'W', 0, 1) }}</span>
                                        @endif
                                    </a>
                                    <div class="p-5">
                                        <p class="text-xs font-bold uppercase tracking-[0.12em] text-accent-700">{{ $relatedArticle?->category ?? 'Story' }}</p>
                                        <h3 class="mt-2 text-lg font-bold leading-snug tracking-[-0.02em] text-neutral-950">
                                            <a href="{{ route('articles.show', $relatedArticle?->slug ?? '') }}" class="transition hover:text-accent-700">{{ $relatedTitle }}</a>
                                        </h3>
                                        <p class="mt-3 text-xs text-neutral-500">
                                            {{ $relatedArticle?->author_name ?? 'Wacanan Writer' }}
                                            <span aria-hidden="true">•</span>
                                            {{ number_format((int) ($relatedArticle?->likes_count ?? 0)) }} likes
                                        </p>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif
        </article>
    @else
        <div class="mx-auto max-w-2xl px-4 py-24 text-center">
            <h1 class="text-3xl font-black text-neutral-950">Story not found.</h1>
            <a href="{{ route('articles.index') }}" class="mt-6 inline-flex rounded-full bg-accent-600 px-5 py-2.5 text-sm font-bold text-white">Browse articles</a>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        (function () {
            var status = document.querySelector('[data-like-status]');

            document.addEventListener('click', async function (event) {
                var button = event.target.closest('[data-like-button]');
                if (!button) return;

                var count = button.querySelector('[data-like-count]');
                var token = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!count || !token) return;

                button.disabled = true;
                if (status) status.textContent = '';

                try {
                    var response = await fetch(button.dataset.likeUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: '{}'
                    });

                    if (!response.ok) throw new Error('Like request failed');
                    var payload = await response.json();
                    var likes = Number.isFinite(payload.likes) ? payload.likes : 0;

                    count.textContent = new Intl.NumberFormat().format(likes);
                    button.setAttribute('aria-pressed', payload.liked ? 'true' : 'false');
                } catch (error) {
                    if (status) status.textContent = 'Could not update the like. Try again.';
                } finally {
                    button.disabled = false;
                }
            });
        })();
    </script>
@endpush
