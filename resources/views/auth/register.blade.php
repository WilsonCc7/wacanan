@extends('layouts.app')

@section('title', 'Create account | Wacanan')

@section('content')
    @php
        $validationErrors = $errors ?? null;
    @endphp

    <div class="mx-auto flex min-h-[calc(100vh-16rem)] max-w-md items-center px-4 py-12 sm:px-6 sm:py-16">
        <section class="w-full rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="register-title">
            <a href="{{ route('home') }}" class="mx-auto grid size-11 place-items-center rounded-xl bg-neutral-950 font-black text-white" aria-label="Wacanan home">W</a>
            <div class="mt-6 text-center">
                <h1 id="register-title" class="text-3xl font-black tracking-[-0.035em] text-neutral-950">Make your first mark.</h1>
                <p class="mt-2 text-sm leading-6 text-neutral-600">Create an account to write, publish, and build a reading community.</p>
            </div>

            @if ($validationErrors?->any())
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert" aria-labelledby="register-errors-title">
                    <h2 id="register-errors-title" class="font-bold">Check the highlighted details.</h2>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($validationErrors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="name" class="text-sm font-bold text-neutral-800">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', '') }}" required autofocus autocomplete="name"
                        @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                        class="mt-2 w-full rounded-xl border bg-neutral-50 px-4 py-3 text-sm text-neutral-950 outline-none transition placeholder:text-neutral-400 focus:ring-4 @error('name') border-red-300 focus:border-red-500 focus:ring-red-500/10 @enderror focus:border-accent-500 focus:bg-white focus:ring-accent-500/10">
                    @error('name')
                        <p id="name-error" class="mt-2 text-xs font-medium text-red-700">{{ $error }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="text-sm font-bold text-neutral-800">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email', '') }}" required autocomplete="email"
                        @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                        class="mt-2 w-full rounded-xl border bg-neutral-50 px-4 py-3 text-sm text-neutral-950 outline-none transition placeholder:text-neutral-400 focus:ring-4 @error('email') border-red-300 focus:border-red-500 focus:ring-red-500/10 @enderror focus:border-accent-500 focus:bg-white focus:ring-accent-500/10">
                    @error('email')
                        <p id="email-error" class="mt-2 text-xs font-medium text-red-700">{{ $error }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="text-sm font-bold text-neutral-800">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                        @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                        class="mt-2 w-full rounded-xl border bg-neutral-50 px-4 py-3 text-sm text-neutral-950 outline-none transition focus:ring-4 @error('password') border-red-300 focus:border-red-500 focus:ring-red-500/10 @enderror focus:border-accent-500 focus:bg-white focus:ring-accent-500/10">
                    @error('password')
                        <p id="password-error" class="mt-2 text-xs font-medium text-red-700">{{ $error }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-neutral-500">Use at least 8 characters.</p>
                </div>

                <div>
                    <label for="password_confirmation" class="text-sm font-bold text-neutral-800">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                        @error('password_confirmation') aria-invalid="true" aria-describedby="password-confirmation-error" @enderror
                        class="mt-2 w-full rounded-xl border bg-neutral-50 px-4 py-3 text-sm text-neutral-950 outline-none transition focus:ring-4 @error('password_confirmation') border-red-300 focus:border-red-500 focus:ring-red-500/10 @enderror focus:border-accent-500 focus:bg-white focus:ring-accent-500/10">
                    @error('password_confirmation')
                        <p id="password-confirmation-error" class="mt-2 text-xs font-medium text-red-700">{{ $error }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full rounded-full bg-accent-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-accent-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-600 focus-visible:ring-offset-2">
                    Create account
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-neutral-600">
                Already have an account?
                <a href="{{ route('login') }}" class="font-bold text-accent-700 transition hover:text-accent-900">Sign in</a>
            </p>
        </section>
    </div>
@endsection
