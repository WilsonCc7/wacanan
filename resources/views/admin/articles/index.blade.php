@extends('layouts.app')

@section('title', 'Articles - Wacanan Admin')

@php
    $icons = [
        'plus' => '<path d="M12 4.5v15m7.5-7.5h-15" />',
        'search' => '<circle cx="11" cy="11" r="7" /><path d="m20 20-3.5-3.5" />',
        'pencil' => '<path d="M16.86 4.49a1.88 1.88 0 1 1 2.65 2.65L7.5 19.14 4 20l.86-3.5L16.86 4.5Z" /><path d="m14.5 6.75 2.75 2.75" />',
        'trash' => '<path d="M4.5 6.5h15M9.5 6.5V5a1.5 1.5 0 0 1 1.5-1.5h2A1.5 1.5 0 0 1 14.5 5v1.5m3 0V18a1.5 1.5 0 0 1-1.5 1.5h-4A1.5 1.5 0 0 1 8 18V6.5" /><path d="M10 10.5v5.5M14 10.5v5.5" />',
        'heart' => '<path d="M21 8.25c0-2.49-2.1-4.5-4.69-4.5-1.93 0-3.6 1.13-4.31 2.74-.71-1.61-2.38-2.74-4.31-2.74C5.1 3.75 3 5.76 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />',
        'check' => '<path d="m4.5 12.75 6 6 9-13.5" />',
        'x' => '<path d="M6 18 18 6M6 6l12 12" />',
        'clock' => '<circle cx="12" cy="12" r="8.5" /><path d="M12 7.5V12l3 1.8" />',
        'doc' => '<path d="M19.5 14.25v-2.63a3.38 3.38 0 0 0-3.38-3.37h-1.5a1.13 1.13 0 0 1-1.12-1.13v-1.5A3.38 3.38 0 0 0 10.12 2.25H8.25m2.25 0H5.63c-.62 0-1.13.5-1.13 1.13v17.25c0 .62.51 1.12 1.13 1.12h12.75c.62 0 1.12-.5 1.12-1.12V11.25a9 9 0 0 0-9-9Z" />',
    ];
    $icon = fn (string $name, string $class = 'size-4') => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '" aria-hidden="true" focusable="false">' . ($icons[$name] ?? '') . '</svg>';

    $activeSearch = request()->query('search', '');
    $activeStatus = request()->query('status', '');
@endphp

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="font-display text-3xl font-bold tracking-tight sm:text-4xl">Articles</h1>
                <p class="mt-1.5 text-sm text-neutral-500">
                    {{ $articles->total() }} {{ Str::plural('story', $articles->total()) }} in the library.
                </p>
            </div>
            <a href="{{ route('admin.articles.create') }}"
                class="inline-flex items-center gap-2 rounded-full bg-neutral-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500/40">
                {!! $icon('plus', 'size-4') !!}
                New article
            </a>
        </div>

        <form action="{{ route('admin.articles.index') }}" method="GET" class="mt-8 flex flex-wrap items-center gap-2" data-auto-submit>
            <div class="relative min-w-0 flex-1 sm:max-w-sm">
                <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400">{!! $icon('search') !!}</span>
                <input type="search" name="search" value="{{ $activeSearch }}" placeholder="Search title, slug or author"
                    aria-label="Search articles"
                    class="w-full rounded-full border border-neutral-200 bg-white py-2.5 pl-11 pr-4 text-sm outline-none transition placeholder:text-neutral-400 focus:border-accent-500 focus:ring-2 focus:ring-accent-500/30">
            </div>

            <label class="sr-only" for="status-filter">Status</label>
            <select id="status-filter" name="status" class="rounded-full border border-neutral-200 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 outline-none transition focus:border-accent-500 focus:ring-2 focus:ring-accent-500/30">
                <option value="">All statuses</option>
                <option value="published" @selected($activeStatus === 'published')>Published</option>
                <option value="draft" @selected($activeStatus === 'draft')>Draft</option>
            </select>

            <button type="submit" class="rounded-full bg-accent-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500/40">Filter</button>

            @if ($activeSearch !== '' || $activeStatus !== '')
                <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center gap-1.5 rounded-full px-4 py-2.5 text-sm font-medium text-neutral-500 transition hover:bg-neutral-100 hover:text-neutral-900">
                    {!! $icon('x', 'size-4') !!}
                    Reset
                </a>
            @endif
        </form>

        <div class="mt-6 overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm">
                @if ($articles->isEmpty())
                    <div class="flex flex-col items-center px-6 py-20 text-center">
                        <span class="grid size-14 place-items-center rounded-2xl bg-accent-50 text-accent-600 ring-1 ring-inset ring-accent-200">
                            {!! $icon('doc', 'size-6') !!}
                        </span>
                        <h2 class="mt-5 font-display text-xl font-bold tracking-tight">
                            {{ $activeSearch !== '' || $activeStatus !== '' ? 'No stories match those filters' : 'No articles yet' }}
                        </h2>
                        <p class="mt-2 max-w-sm text-sm leading-relaxed text-neutral-500">
                            {{ $activeSearch !== '' || $activeStatus !== ''
                                ? 'Try a different search term, or clear the status filter to see the whole library.'
                                : 'Draft the first story, add a cover, and publish it when the words are ready.' }}
                        </p>
                        <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
                            <a href="{{ route('admin.articles.create') }}"
                                class="inline-flex items-center gap-2 rounded-full bg-neutral-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-accent-700">
                                {!! $icon('plus', 'size-4') !!}
                                Write the first article
                            </a>
                            @if ($activeSearch !== '' || $activeStatus !== '')
                                <a href="{{ route('admin.articles.index') }}" class="rounded-full border border-neutral-200 px-5 py-2.5 text-sm font-semibold text-neutral-700 transition hover:bg-neutral-100">
                                    Show all articles
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[46rem] border-collapse text-left text-sm">
                            <thead>
                                <tr class="border-b border-neutral-200 bg-neutral-50/80 text-xs font-semibold uppercase tracking-[0.12em] text-neutral-500">
                                    <th scope="col" class="w-10 px-4 py-3.5">
                                        <input type="checkbox" form="article-bulk" data-bulk-all aria-label="Select all articles"
                                            class="size-4 cursor-pointer rounded border-neutral-300 text-accent-600 focus:ring-accent-500/50">
                                    </th>
                                    <th scope="col" class="px-3 py-3.5">Story</th>
                                    <th scope="col" class="px-3 py-3.5">Category</th>
                                    <th scope="col" class="px-3 py-3.5 text-right">Likes</th>
                                    <th scope="col" class="px-3 py-3.5">Status</th>
                                    <th scope="col" class="px-3 py-3.5">Updated</th>
                                    <th scope="col" class="px-4 py-3.5 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                @foreach ($articles as $article)
                                    @php
                                        $isScheduled = $article->published_at && $article->published_at->isFuture();
                                        $status = $article->published_at === null ? 'draft' : ($isScheduled ? 'scheduled' : 'published');
                                    @endphp
                                    <tr class="transition hover:bg-accent-50/40" data-row>
                                        <td class="px-4 py-3.5 align-middle">
                                            <input type="checkbox" form="article-bulk" name="ids[]" value="{{ $article->id }}" data-bulk-item
                                                aria-label="Select {{ $article->title }}"
                                                class="size-4 cursor-pointer rounded border-neutral-300 text-accent-600 focus:ring-accent-500/50">
                                        </td>
                                        <td class="px-3 py-3.5">
                                            <div class="flex items-center gap-3">
                                                @if ($article->coverUrl())
                                                    <img src="{{ $article->coverUrl() }}" alt="" loading="lazy"
                                                        class="size-12 shrink-0 rounded-xl object-cover ring-1 ring-inset ring-neutral-200">
                                                @else
                                                    <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-neutral-100 text-neutral-400 ring-1 ring-inset ring-neutral-200">
                                                        {!! $icon('doc') !!}
                                                    </span>
                                                @endif
                                                <div class="min-w-0">
                                                    <a href="{{ route('admin.articles.edit', $article) }}"
                                                        class="block max-w-[22rem] truncate font-semibold text-neutral-900 transition hover:text-accent-700">
                                                        {{ $article->title }}
                                                    </a>
                                                    <p class="max-w-[22rem] truncate font-mono text-xs text-neutral-400">/{{ $article->slug }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3.5">
                                            <span class="inline-flex items-center rounded-full bg-accent-50 px-3 py-1 text-xs font-semibold text-accent-700 ring-1 ring-inset ring-accent-200">
                                                {{ $article->category }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3.5 text-right tabular-nums text-neutral-600">
                                            <span class="inline-flex items-center gap-1.5">
                                                {!! $icon('heart', 'size-4 text-neutral-400') !!}
                                                {{ number_format($article->likes_count) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3.5">
                                            <span @class([
                                                'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset',
                                                'bg-accent-50 text-accent-700 ring-accent-200' => $status === 'published',
                                                'bg-neutral-100 text-neutral-600 ring-neutral-200' => $status === 'draft',
                                                'bg-amber-50 text-amber-700 ring-amber-200' => $status === 'scheduled',
                                            ])>
                                                {!! $icon('clock', 'size-3.5') !!}
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3.5 whitespace-nowrap text-neutral-500">{{ $article->updated_at->diffForHumans() }}</td>
                                        <td class="px-4 py-3.5">
                                            <div class="flex items-center justify-end gap-1">
                                                <a href="{{ route('admin.articles.edit', $article) }}" title="Edit {{ $article->title }}"
                                                    aria-label="Edit {{ $article->title }}"
                                                    class="grid size-9 place-items-center rounded-full text-neutral-500 transition hover:bg-accent-50 hover:text-accent-700">
                                                    {!! $icon('pencil') !!}
                                                </a>
                                                <form action="{{ route('admin.articles.destroy', $article) }}" method="POST"
                                                    data-confirm="Delete “{{ $article->title }}”? This cannot be undone.">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Delete {{ $article->title }}" aria-label="Delete {{ $article->title }}"
                                                        class="grid size-9 place-items-center rounded-full text-neutral-500 transition hover:bg-red-50 hover:text-red-600">
                                                        {!! $icon('trash') !!}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        <form id="article-bulk" action="{{ route('admin.articles.bulk') }}" method="POST" data-bulk-form>
            @csrf

            <div hidden data-bulk-bar
                class="mt-4 flex flex-wrap items-center gap-3 rounded-2xl border border-neutral-900/10 bg-neutral-900 px-4 py-3 text-white shadow-lg sm:px-5">
                <p class="text-sm font-medium"><span data-bulk-count>0</span> selected</p>
                <div class="ml-auto flex flex-wrap items-center gap-2">
                    <button type="button" data-bulk-clear class="rounded-full px-3 py-2 text-sm font-medium text-neutral-300 transition hover:bg-white/10 hover:text-white">Clear</button>
                    <button type="submit" name="action" value="publish" data-bulk-submit
                        class="inline-flex items-center gap-1.5 rounded-full bg-accent-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-accent-400">
                        {!! $icon('check', 'size-4') !!}
                        Publish
                    </button>
                    <button type="submit" name="action" value="delete" data-bulk-submit data-bulk-danger
                        class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-white ring-1 ring-inset ring-white/20 transition hover:bg-red-500">
                        {!! $icon('trash', 'size-4') !!}
                        Delete
                    </button>
                </div>
            </div>
        </form>

        @if ($articles->hasPages())
            <div class="mt-8">{{ $articles->onEachSide(1)->links() }}</div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            // auto-submit the filter form when the status select changes
            document.addEventListener('change', function (event) {
                var field = event.target;
                if (!field.closest('[data-auto-submit]') || field.tagName !== 'SELECT') return;
                if (field.form) field.form.submit();
            });

            // destructive confirmations
            document.addEventListener('submit', function (event) {
                var message = event.target.getAttribute && event.target.getAttribute('data-confirm');
                if (message && !window.confirm(message)) event.preventDefault();
            });

            var form = document.querySelector('[data-bulk-form]');
            if (!form) return;

            var bar = document.querySelector('[data-bulk-bar]');
            var master = document.querySelector('[data-bulk-all]');
            var counter = document.querySelector('[data-bulk-count]');
            var clear = document.querySelector('[data-bulk-clear]');

            function items() {
                return document.querySelectorAll('[data-bulk-item]');
            }

            function sync() {
                var all = Array.prototype.slice.call(items());
                var picked = all.filter(function (box) { return box.checked; });

                if (bar) bar.hidden = picked.length === 0;
                if (counter) counter.textContent = String(picked.length);
                if (master) {
                    master.checked = picked.length > 0 && picked.length === all.length;
                    master.indeterminate = picked.length > 0 && picked.length < all.length;
                }

                form.querySelectorAll('[data-bulk-submit]').forEach(function (button) {
                    button.disabled = picked.length === 0;
                });
            }

            document.addEventListener('change', function (event) {
                var box = event.target;
                if (!box.hasAttribute || (!box.hasAttribute('data-bulk-all') && !box.hasAttribute('data-bulk-item'))) return;

                if (box === master) {
                    Array.prototype.forEach.call(items(), function (item) { item.checked = master.checked; });
                }

                sync();
            });

            if (clear) {
                clear.addEventListener('click', function () {
                    items().forEach(function (item) { item.checked = false; });
                    sync();
                });
            }

            form.addEventListener('submit', function (event) {
                var button = event.submitter;
                if (button && button.hasAttribute('data-bulk-danger') && !window.confirm('Delete the selected articles? This cannot be undone.')) {
                    event.preventDefault();
                }
            });

            sync();
        })();
    </script>
@endpush
