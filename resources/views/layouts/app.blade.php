<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Wacanan')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full flex-col bg-neutral-50 font-sans text-neutral-900 antialiased">
    <header class="sticky top-0 z-50 border-b border-neutral-200/70 bg-white/85 backdrop-blur-lg">
        <div class="mx-auto flex h-16 max-w-7xl items-center gap-3 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2" aria-label="Wacanan home">
                <span class="grid size-8 place-items-center rounded-xl bg-neutral-900 text-sm font-bold text-white">W</span>
                <span class="text-lg font-bold tracking-tight">Wacanan</span>
            </a>

            <form action="{{ route('articles.index') }}" method="GET" class="relative ml-2 hidden max-w-md flex-1 md:block" role="search">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-neutral-400" />
                <input type="search" name="search" value="{{ request()->input('search', '') }}" placeholder="Search articles" aria-label="Search articles"
                    class="w-full rounded-full border border-neutral-200 bg-neutral-100 py-2 pl-10 pr-4 text-sm outline-none transition placeholder:text-neutral-400 focus:border-accent-500 focus:bg-white focus:ring-2 focus:ring-accent-500/30">
            </form>

            <div class="ml-auto flex items-center gap-1 sm:gap-2">
                @guest
                    <a href="{{ route('login') }}" class="rounded-full px-3 py-2 text-sm font-medium text-neutral-600 transition hover:bg-neutral-100 hover:text-neutral-900">Sign in</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-neutral-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-neutral-800">Start writing</a>
                @else
                    <a href="{{ route('admin.articles.create') }}" class="rounded-full bg-neutral-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-neutral-800">Start writing</a>
                    <a href="#" aria-label="Saved articles" class="grid size-9 place-items-center rounded-full text-neutral-600 transition hover:bg-neutral-100 hover:text-neutral-900">
                        <x-icon name="bookmark" class="size-5" />
                    </a>
                    <a href="#" aria-label="Notifications" class="grid size-9 place-items-center rounded-full text-neutral-600 transition hover:bg-neutral-100 hover:text-neutral-900">
                        <x-icon name="bell" class="size-5" />
                    </a>
                    <x-avatar :name="auth()->user()->name" class="size-9 text-xs" />
                @endguest
            </div>
        </div>

        @isset($categories)
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center gap-1.5 overflow-x-auto border-t border-neutral-100 py-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                    aria-label="Categories">
                    <a href="{{ route('articles.index') }}" @class([
                        'shrink-0 rounded-full px-3.5 py-1.5 text-sm font-medium transition',
                        'bg-neutral-900 text-white' => ($activeCategory ?? null) === null,
                        'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900' => ($activeCategory ?? null) !== null,
                    ])>All</a>
                    @foreach ($categories as $category)
                        <a href="{{ route('articles.index', ['category' => $category]) }}" @class([
                            'shrink-0 rounded-full px-3.5 py-1.5 text-sm font-medium transition',
                            'bg-neutral-900 text-white' => ($activeCategory ?? null) === $category,
                            'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900' => ($activeCategory ?? null) !== $category,
                        ])>{{ $category }}</a>
                    @endforeach
                </nav>
            </div>
        @endisset
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="border-t border-neutral-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-8 md:grid-cols-3">
                <div>
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <span class="grid size-8 place-items-center rounded-xl bg-neutral-900 text-sm font-bold text-white">W</span>
                        <span class="text-lg font-bold tracking-tight">Wacanan</span>
                    </a>
                    <p class="mt-3 max-w-xs text-sm leading-relaxed text-neutral-600">A place to read, write, and share stories that matter.</p>
                </div>

                <div>
                    <h2 class="text-xs font-bold uppercase tracking-[0.15em] text-neutral-500">Explore</h2>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        <li><a href="{{ route('home') }}" class="font-medium text-neutral-700 transition hover:text-accent-700">Home</a></li>
                        <li><a href="{{ route('articles.index') }}" class="font-medium text-neutral-700 transition hover:text-accent-700">Articles</a></li>
                        <li><a href="{{ auth()->check() ? route('admin.articles.create') : route('register') }}" class="font-medium text-neutral-700 transition hover:text-accent-700">Start writing</a></li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-xs font-bold uppercase tracking-[0.15em] text-neutral-500">Topics</h2>
                    <ul class="mt-4 flex flex-wrap gap-x-5 gap-y-2.5 text-sm">
                        @isset($categories)
                            @foreach ($categories as $category)
                                <li>
                                    <a href="{{ route('articles.index', ['category' => $category]) }}" class="font-medium text-neutral-700 transition hover:text-accent-700">{{ $category }}</a>
                                </li>
                            @endforeach
                        @endisset
                    </ul>
                </div>
            </div>

            <div class="mt-10 flex flex-col items-center justify-between gap-3 border-t border-neutral-100 pt-6 sm:flex-row">
                <p class="text-sm text-neutral-500">&copy; {{ date('Y') }} Wacanan. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-neutral-500">Built with Laravel.</span>
                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-neutral-500 transition hover:text-neutral-900">Sign out</button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </footer>

    @push('scripts')
        <script>
            (function () {
                // copy link feedback
                document.addEventListener('click', function (e) {
                    var trigger = e.target.closest('[data-copy-url]');
                    if (!trigger) return;
                    e.preventDefault();
                    var label = trigger.querySelector('[data-copy-label]');
                    var restore = function () { if (label) label.textContent = label.dataset.original; };
                    var mark = function (text) { if (label) { label.dataset.original = label.dataset.original || label.textContent; label.textContent = text; } };
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(trigger.dataset.copyUrl).then(function () {
                            mark('Copied'); setTimeout(restore, 1600);
                        }, function () { mark('Select and copy'); setTimeout(restore, 1600); });
                    } else {
                        mark('Select and copy'); setTimeout(restore, 1600);
                    }
                });

                // local-only follow placeholder (no follow endpoint in scope yet)
                document.addEventListener('click', function (e) {
                    var btn = e.target.closest('[data-follow]');
                    if (!btn) return;
                    e.preventDefault();
                    var on = btn.querySelector('[data-follow-on]');
                    var off = btn.querySelector('[data-follow-off]');
                    if (on && off) {
                        on.classList.toggle('hidden');
                        off.classList.toggle('hidden');
                    }
                });
            })();
        </script>
    @endpush

    @stack('scripts')
</body>
</html>
