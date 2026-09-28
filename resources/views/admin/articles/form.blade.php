@extends('layouts.app')

@php
    $isEdit = $article !== null;
    $selectedCategory = old('category', $article->category ?? '');
    $coverUrl = $isEdit ? $article->coverUrl() : '';
    $publishedAt = old('published_at', $isEdit && $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '');
    $currentStatus = old('status', $publishedAt !== '' && $publishedAt !== null ? 'published' : 'draft');

    $icons = [
        'arrow-left' => '<path d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />',
        'check' => '<path d="m4.5 12.75 6 6 9-13.5" />',
        'x' => '<path d="M6 18 18 6M6 6l12 12" />',
        'image' => '<rect x="3" y="4.5" width="18" height="15" rx="2.5" /><circle cx="8.5" cy="9.5" r="1.5" /><path d="m4.5 17 4.2-4.2a2 2 0 0 1 2.8 0l3.2 3.2m0 0 1.6-1.6a2 2 0 0 1 2.8 0l.9.9" />',
        'upload' => '<path d="M12 15.5V4m0 0L7.5 8.5M12 4l4.5 4.5M4.5 15v2.5a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V15" />',
        'bold' => '<path d="M7 4.5h5.25a3.75 3.75 0 0 1 0 7.5H7Zm0 7.5h6a3.75 3.75 0 0 1 0 7.5H7Z" />',
        'italic' => '<path d="M14.5 4.5h-5m3 0-3 15m5 0h-5" />',
        'quote' => '<path d="M9.5 6.5H6.75A2.25 2.25 0 0 0 4.5 8.75v.5A2.25 2.25 0 0 0 6.75 11.5h2A2.25 2.25 0 0 1 11 13.75V17H6.75A2.25 2.25 0 0 1 4.5 14.75m15-8.25H16.5A2.25 2.25 0 0 0 14.25 8.75v.5a2.25 2.25 0 0 0 2.25 2.25h1.25a2.25 2.25 0 0 1 2.25 2.25V17h-4.25A2.25 2.25 0 0 1 13.5 14.75" />',
        'list' => '<path d="M9 7h11M9 12h11M9 17h11M4.5 7h.01M4.5 12h.01M4.5 17h.01" />',
        'search' => '<circle cx="11" cy="11" r="7" /><path d="m20 20-3.5-3.5" />',
    ];
    $icon = fn (string $name, string $class = 'size-4') => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '" aria-hidden="true" focusable="false">' . ($icons[$name] ?? '') . '</svg>';

    $field = 'w-full rounded-2xl border border-neutral-200 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition placeholder:text-neutral-400 focus:border-accent-500 focus:ring-2 focus:ring-accent-500/25';
@endphp

@section('title', ($isEdit ? 'Edit article' : 'New article') . ' - Wacanan Admin')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-neutral-500 transition hover:text-accent-700">
            {!! $icon('arrow-left') !!}
            Back to articles
        </a>

        <div class="mt-5 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="font-display text-3xl font-bold tracking-tight sm:text-4xl">{{ $isEdit ? 'Edit article' : 'New article' }}</h1>
                <p class="mt-1.5 text-sm text-neutral-500">
                    {{ $isEdit ? 'Shape the story, then send it back out into the world.' : 'Start with a sharp title. The rest can follow.' }}
                </p>
            </div>
            @if ($isEdit)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-neutral-500 ring-1 ring-inset ring-neutral-200">
                    {{ $article->slug }}
                </span>
            @endif
        </div>

        @if (($errors ?? null)?->any())
            <div role="alert" class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-800">
                <p class="font-semibold">Fix {{ $errors->count() }} {{ Str::plural('field', $errors->count()) }} before saving.</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ $isEdit ? route('admin.articles.update', $article) : route('admin.articles.store') }}" method="POST" enctype="multipart/form-data"
            class="mt-8 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_21rem]" data-article-form>
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="space-y-6">
                <section class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm sm:p-7">
                    <h2 class="font-display text-lg font-bold tracking-tight">The headline</h2>

                    <label for="title" class="mt-5 block text-sm font-semibold text-neutral-800">Title</label>
                    <input id="title" name="title" type="text" required maxlength="180" value="{{ old('title', $article->title ?? '') }}"
                        placeholder="A title readers cannot scroll past" autocomplete="off" data-slug-source
                        class="{{ $field }} mt-2 text-base font-semibold">

                    <label for="slug" class="mt-5 block text-sm font-semibold text-neutral-800">Slug</label>
                    <input id="slug" name="slug" type="text" value="{{ old('slug', $article->slug ?? '') }}" placeholder="a-title-readers-cannot-scroll-past"
                        autocomplete="off" data-slug-input class="{{ $field }} mt-2 font-mono text-sm">
                    <p class="mt-2 text-xs text-neutral-500" data-slug-preview>Preview: /articles/<span data-slug-value>{{ old('slug', $article->slug ?? 'untitled-story') }}</span></p>
                </section>

                <section class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm sm:p-7">
                    <h2 class="font-display text-lg font-bold tracking-tight">The story</h2>

                    <div class="mt-5 flex items-center justify-between gap-3">
                        <label for="excerpt" class="text-sm font-semibold text-neutral-800">Excerpt</label>
                        <span class="text-xs font-medium tabular-nums text-neutral-500"><span data-excerpt-count>0</span>/160</span>
                    </div>
                    <textarea id="excerpt" name="excerpt" rows="3" maxlength="160" data-excerpt
                        placeholder="One or two lines that earn the click." class="{{ $field }} mt-2 resize-y">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>

                    <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                        <label for="body" class="text-sm font-semibold text-neutral-800">Body</label>
                        <div class="flex items-center gap-1 rounded-full border border-neutral-200 bg-neutral-50 p-1" role="group" aria-label="Body formatting">
                            <button type="button" data-format="bold" title="Bold" aria-label="Bold" class="grid size-8 place-items-center rounded-full text-neutral-600 transition hover:bg-white hover:text-accent-700">
                                {!! $icon('bold', 'size-4') !!}
                            </button>
                            <button type="button" data-format="italic" title="Italic" aria-label="Italic" class="grid size-8 place-items-center rounded-full text-neutral-600 transition hover:bg-white hover:text-accent-700">
                                {!! $icon('italic', 'size-4') !!}
                            </button>
                            <button type="button" data-format="quote" title="Quote" aria-label="Quote" class="grid size-8 place-items-center rounded-full text-neutral-600 transition hover:bg-white hover:text-accent-700">
                                {!! $icon('quote', 'size-4') !!}
                            </button>
                            <button type="button" data-format="list" title="Bulleted list" aria-label="Bulleted list" class="grid size-8 place-items-center rounded-full text-neutral-600 transition hover:bg-white hover:text-accent-700">
                                {!! $icon('list', 'size-4') !!}
                            </button>
                        </div>
                    </div>
                    <textarea id="body" name="body" rows="16" required data-body
                        placeholder="Write freely. Blank lines separate paragraphs." class="{{ $field }} mt-2 min-h-[22rem] resize-y font-display leading-relaxed">{{ old('body', $article->body ?? '') }}</textarea>
                    <p class="mt-2 text-xs text-neutral-500"><span data-word-count>0</span> words</p>
                </section>

                <section class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm sm:p-7">
                    <h2 class="font-display text-lg font-bold tracking-tight">Cover and topic</h2>

                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="cover_image" class="text-sm font-semibold text-neutral-800">Cover image</label>
                            <div data-dropzone
                                class="mt-2 cursor-pointer rounded-2xl border-2 border-dashed border-neutral-200 bg-neutral-50 px-4 py-6 text-center transition hover:border-accent-400 hover:bg-accent-50/50">
                                <span class="mx-auto grid size-10 place-items-center rounded-full bg-white text-accent-600 ring-1 ring-inset ring-neutral-200">
                                    {!! $icon('upload') !!}
                                </span>
                                <p class="mt-3 text-sm font-medium text-neutral-700">Drop an image here, or <span class="text-accent-700">browse</span></p>
                                <p class="mt-1 text-xs text-neutral-500">JPG, PNG or WebP, up to 4 MB.</p>
                                <img data-cover-preview src="" alt="Selected cover preview" hidden
                                    class="mt-4 w-full rounded-xl object-cover ring-1 ring-inset ring-neutral-200">
                            </div>
                            <input id="cover_image" name="cover_image" type="file" accept="image/jpeg,image/png,image/webp" data-cover-input
                                class="mt-3 block w-full text-xs text-neutral-500 file:mr-3 file:rounded-full file:border-0 file:bg-neutral-900 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-white transition hover:file:bg-accent-700">
                        </div>

                        <div>
                            <label for="category" class="text-sm font-semibold text-neutral-800">Category</label>
                            <select id="category" name="category" required data-category class="{{ $field }} mt-2">
                                <option value="">Choose a category</option>
                                @foreach ($categories as $option)
                                    <option value="{{ $option }}" @selected($selectedCategory === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-xs text-neutral-500">Categories shape the feed and the topic pills.</p>

                            <div @if (! $coverUrl) hidden @endif data-current-cover class="mt-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-neutral-500">Current cover</p>
                                <img src="{{ $coverUrl }}" alt="Current cover" class="mt-2 w-full rounded-xl object-cover ring-1 ring-inset ring-neutral-200">
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <aside class="space-y-4 lg:sticky lg:top-24">
                <section class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
                    <h2 class="font-display text-base font-bold tracking-tight">Publishing</h2>

                    <label for="status" class="mt-4 block text-sm font-semibold text-neutral-800">Status</label>
                    <select id="status" name="status" data-status class="{{ $field }} mt-2">
                        <option value="draft" @selected($currentStatus === 'draft')>Draft</option>
                        <option value="published" @selected($currentStatus === 'published')>Published</option>
                    </select>

                    <div data-publish-schedule @if ($currentStatus === 'draft') hidden @endif class="mt-4">
                        <label for="published_at" class="text-sm font-semibold text-neutral-800">Publish at</label>
                        <input id="published_at" name="published_at" type="datetime-local" value="{{ $publishedAt }}" data-published-at
                            class="{{ $field }} mt-2">
                        <p class="mt-2 text-xs text-neutral-500">Leave empty to publish as soon as you save.</p>
                    </div>

                    <div class="mt-6 flex flex-col gap-2">
                        <button type="submit" name="status" value="published" data-publish-button
                            class="inline-flex items-center justify-center gap-2 rounded-full bg-accent-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500/40">
                            {!! $icon('check') !!}
                            {{ $isEdit ? 'Publish changes' : 'Publish article' }}
                        </button>
                        <button type="submit" name="status" value="draft" data-draft-button
                            class="rounded-full border border-neutral-200 px-5 py-3 text-sm font-semibold text-neutral-700 transition hover:border-accent-300 hover:bg-accent-50 hover:text-accent-700">
                            Save as draft
                        </button>
                    </div>
                </section>

                <section class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
                    <h2 class="font-display text-base font-bold tracking-tight">Search preview</h2>
                    <p class="mt-1 text-xs text-neutral-500">How the story can appear in search.</p>

                    <div class="mt-4 rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                        <p class="truncate text-xs text-neutral-500">{{ request()->getHost() }}/articles/<span data-serp-slug>untitled-story</span></p>
                        <p data-serp-title class="mt-1 line-clamp-2 text-base font-semibold leading-snug text-[#1a0dab]">Untitled story</p>
                        <p data-serp-excerpt class="mt-1 line-clamp-2 text-xs leading-relaxed text-neutral-600">Add an excerpt so readers know what they will get.</p>
                    </div>

                    <ul class="mt-4 space-y-2 text-xs">
                        <li class="flex items-start gap-2" data-seo-check="title">
                            <span class="mt-0.5 text-neutral-400" data-seo-icon>{!! $icon('x', 'size-4') !!}</span>
                            <span data-seo-label class="text-neutral-600">Title length between 30 and 60 characters</span>
                        </li>
                        <li class="flex items-start gap-2" data-seo-check="description">
                            <span class="mt-0.5 text-neutral-400" data-seo-icon>{!! $icon('x', 'size-4') !!}</span>
                            <span data-seo-label class="text-neutral-600">Excerpt length between 70 and 160 characters</span>
                        </li>
                        <li class="flex items-start gap-2" data-seo-check="slug">
                            <span class="mt-0.5 text-neutral-400" data-seo-icon>{!! $icon('x', 'size-4') !!}</span>
                            <span data-seo-label class="text-neutral-600">Slug is short and readable</span>
                        </li>
                    </ul>
                </section>

                <section class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
                    <h2 class="font-display text-base font-bold tracking-tight">Ready check</h2>
                    <p class="mt-1 text-xs text-neutral-500">{{ $isEdit ? 'Everything needed before readers see it.' : 'Five things make a story feel finished.' }}</p>

                    <ul class="mt-4 space-y-2.5 text-sm">
                        @foreach ([
                            'title' => 'A clear, specific title',
                            'excerpt' => 'An excerpt under 160 characters',
                            'category' => 'A category is selected',
                            'cover' => 'A cover image is attached',
                            'body' => 'At least 50 words of body copy',
                        ] as $key => $label)
                            <li class="flex items-start gap-2.5" data-check="{{ $key }}">
                                <span class="mt-0.5 grid size-4 shrink-0 place-items-center rounded-full bg-neutral-100 text-neutral-400" data-check-icon>{!! $icon('check', 'size-3') !!}</span>
                                <span data-check-label class="text-neutral-600">{{ $label }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            </aside>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            'use strict';

            var form = document.querySelector('[data-article-form]');
            if (!form) return;

            var $ = function (selector) { return form.querySelector(selector); };

            /* slug preview ---------------------------------------------------- */
            var titleInput = $('[data-slug-source]');
            var slugInput = $('[data-slug-input]');
            var slugValue = document.querySelector('[data-slug-value]');
            var slugTouched = slugInput ? slugInput.value.trim() !== '' : false;

            function slugify(value) {
                return value
                    .toLowerCase()
                    .normalize('NFD').replace(/[̀-ͯ]/g, '')
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }

            function renderSlug(value) {
                var next = value || 'untitled-story';
                if (slugValue) slugValue.textContent = next;
                var serp = document.querySelector('[data-serp-slug]');
                if (serp) serp.textContent = next;
            }

            if (titleInput) {
                titleInput.addEventListener('input', function () {
                    if (slugTouched || !slugInput) return;
                    var next = slugify(titleInput.value);
                    slugInput.value = next;
                    renderSlug(next);
                });
            }

            if (slugInput) {
                slugInput.addEventListener('input', function () {
                    slugTouched = true;
                    renderSlug(slugInput.value.trim());
                });
            }

            renderSlug(slugInput && slugInput.value.trim() ? slugInput.value.trim() : slugify(titleInput ? titleInput.value : '') || 'untitled-story');

            /* counters -------------------------------------------------------- */
            var excerpt = $('[data-excerpt]');
            var excerptCount = document.querySelector('[data-excerpt-count]');
            var body = $('[data-body]');
            var wordCount = document.querySelector('[data-word-count]');

            function countWords(value) {
                var trimmed = (value || '').trim();
                return trimmed === '' ? 0 : trimmed.split(/\s+/).length;
            }

            if (excerpt && excerptCount) {
                excerptCount.textContent = String(excerpt.value.length);
                excerpt.addEventListener('input', function () {
                    excerptCount.textContent = String(excerpt.value.length);
                });
            }

            if (body && wordCount) {
                wordCount.textContent = String(countWords(body.value));
                body.addEventListener('input', function () {
                    wordCount.textContent = String(countWords(body.value));
                });
            }

            /* body toolbar ---------------------------------------------------- */
            function prefixLines(area, prefix) {
                var value = area.value;
                var start = value.lastIndexOf('\n', area.selectionStart - 1) + 1;
                var end = area.selectionEnd;
                var block = value.slice(start, end) || '';
                var replaced = block.split('\n').map(function (line) {
                    return line.indexOf(prefix) === 0 ? line.slice(prefix.length) : prefix + line;
                }).join('\n');
                area.setRangeText(replaced, start, end, 'end');
                area.dispatchEvent(new Event('input', { bubbles: true }));
            }

            function wrapSelection(area, marker) {
                var start = area.selectionStart;
                var end = area.selectionEnd;
                var selected = area.value.slice(start, end) || 'text';
                area.setRangeText(marker + selected + marker, start, end, 'select');
                area.focus();
                area.dispatchEvent(new Event('input', { bubbles: true }));
            }

            form.querySelectorAll('[data-format]').forEach(function (button) {
                button.addEventListener('click', function () {
                    if (!body) return;
                    body.focus();
                    var format = button.getAttribute('data-format');
                    if (format === 'bold') wrapSelection(body, '**');
                    else if (format === 'italic') wrapSelection(body, '*');
                    else if (format === 'quote') prefixLines(body, '> ');
                    else if (format === 'list') prefixLines(body, '- ');
                });
            });

            /* cover drag and drop --------------------------------------------- */
            var dropzone = form.querySelector('[data-dropzone]');
            var coverInput = form.querySelector('[data-cover-input]');
            var coverPreview = document.querySelector('[data-cover-preview]');
            var currentCover = document.querySelector('[data-current-cover]');
            var coverObjectUrl = null;

            function showCover(file) {
                if (!file || !file.type || file.type.indexOf('image/') !== 0) return;
                if (coverObjectUrl) URL.revokeObjectURL(coverObjectUrl);
                coverObjectUrl = URL.createObjectURL(file);
                if (coverPreview) {
                    coverPreview.src = coverObjectUrl;
                    coverPreview.hidden = false;
                }
                if (currentCover) currentCover.hidden = true;
            }

            if (coverInput) {
                coverInput.addEventListener('change', function () {
                    showCover(coverInput.files && coverInput.files[0]);
                });
            }

            if (dropzone) {
                ['dragenter', 'dragover'].forEach(function (type) {
                    dropzone.addEventListener(type, function (event) {
                        event.preventDefault();
                        dropzone.classList.add('border-accent-500', 'bg-accent-50');
                    });
                });

                ['dragleave', 'drop'].forEach(function (type) {
                    dropzone.addEventListener(type, function () {
                        dropzone.classList.remove('border-accent-500', 'bg-accent-50');
                    });
                });

                dropzone.addEventListener('drop', function (event) {
                    event.preventDefault();
                    var files = event.dataTransfer && event.dataTransfer.files;
                    if (!coverInput || !files || !files.length) return;
                    coverInput.files = files;
                    showCover(files[0]);
                });
            }

            /* publish card ----------------------------------------------------- */
            var status = $('[data-status]');
            var schedule = form.querySelector('[data-publish-schedule]');
            var publishedAt = form.querySelector('[data-published-at]');

            function renderStatus(value) {
                var published = value === 'published';
                if (schedule) schedule.hidden = !published;
                if (publishedAt) publishedAt.disabled = !published;
            }

            if (status) {
                renderStatus(status.value);
                status.addEventListener('change', function () { renderStatus(status.value); });
            }

            function nowLocal() {
                var now = new Date();
                var pad = function (number) { return String(number).padStart(2, '0'); };
                return now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()) + 'T' + pad(now.getHours()) + ':' + pad(now.getMinutes());
            }

            form.addEventListener('submit', function (event) {
                var submitter = event.submitter;
                var wanted = submitter && submitter.getAttribute('name') === 'status' ? submitter.value : (status ? status.value : 'draft');

                if (status) status.value = wanted;
                renderStatus(wanted);

                if (wanted === 'draft') {
                    if (publishedAt) {
                        publishedAt.value = '';
                        publishedAt.disabled = true;
                    }
                } else if (publishedAt && !publishedAt.value) {
                    publishedAt.value = nowLocal();
                    publishedAt.disabled = false;
                }
            });

            /* SEO preview and checks ------------------------------------------- */
            var serpTitle = document.querySelector('[data-serp-title]');
            var serpExcerpt = document.querySelector('[data-serp-excerpt]');
            var category = form.querySelector('[data-category]');

            function markRow(row, passed) {
                if (!row) return;
                var icon = row.querySelector('[data-seo-icon]');
                var label = row.querySelector('[data-seo-label]');
                row.classList.toggle('text-accent-700', passed);
                row.classList.toggle('text-neutral-600', !passed);
                if (icon) {
                    icon.className = 'mt-0.5 ' + (passed ? 'text-accent-600' : 'text-neutral-400');
                    icon.innerHTML = passed
                        ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true"><path d="m4.5 12.75 6 6 9-13.5" /></svg>'
                        : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true"><path d="M6 18 18 6M6 6l12 12" /></svg>';
                }
                if (label) label.className = passed ? 'text-neutral-700' : 'text-neutral-600';
            }

            function seoRow(name) {
                return document.querySelector('[data-seo-check="' + name + '"]');
            }

            function renderSeo() {
                var title = titleInput ? titleInput.value.trim() : '';
                var excerptValue = excerpt ? excerpt.value.trim() : '';
                var slug = slugInput ? slugInput.value.trim() : '';

                if (serpTitle) serpTitle.textContent = title || 'Untitled story';
                if (serpExcerpt) serpExcerpt.textContent = excerptValue || 'Add an excerpt so readers know what they will get.';

                markRow(seoRow('title'), title.length >= 30 && title.length <= 60);
                markRow(seoRow('description'), excerptValue.length >= 70 && excerptValue.length <= 160);
                markRow(seoRow('slug'), slug !== '' && slug.length <= 72 && !/\s/.test(slug));
            }

            /* checklist --------------------------------------------------------- */
            function markCheck(key, passed) {
                var row = document.querySelector('[data-check="' + key + '"]');
                if (!row) return;
                var icon = row.querySelector('[data-check-icon]');
                var label = row.querySelector('[data-check-label]');
                if (icon) {
                    icon.className = 'mt-0.5 grid size-4 shrink-0 place-items-center rounded-full ' + (passed ? 'bg-accent-600 text-white' : 'bg-neutral-100 text-neutral-400');
                }
                if (label) label.className = passed ? 'text-neutral-800' : 'text-neutral-600';
            }

            function renderChecklist() {
                var title = titleInput ? titleInput.value.trim() : '';
                markCheck('title', title.length >= 10);
                markCheck('excerpt', !!excerpt && excerpt.value.trim().length > 0 && excerpt.value.length <= 160);
                markCheck('category', !!category && category.value !== '');
                markCheck('cover', !!coverInput && coverInput.files && coverInput.files.length > 0 ? true : (coverPreview && !coverPreview.hidden) || (currentCover && !currentCover.hidden));
                markCheck('body', countWords(body ? body.value : '') >= 50);
            }

            function renderAll() {
                renderSeo();
                renderChecklist();
            }

            if (titleInput) titleInput.addEventListener('input', renderAll);
            if (excerpt) excerpt.addEventListener('input', renderAll);
            if (body) body.addEventListener('input', renderAll);
            if (category) category.addEventListener('change', renderAll);
            if (coverInput) coverInput.addEventListener('change', renderAll);
            if (slugInput) slugInput.addEventListener('input', renderAll);

            renderAll();
        })();
    </script>
@endpush
