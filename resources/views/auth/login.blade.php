@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
    <div class="mx-auto flex min-h-[calc(100vh-8rem)] max-w-md flex-col justify-center px-4 py-12">
        <div class="mb-6 text-center">
            <span class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-primary-600 text-xl font-bold text-white">P</span>
            <h1 class="mt-4 text-2xl font-bold text-slate-900">Masuk ke Akun</h1>
            <p class="mt-1 text-sm text-slate-500">Panel pustakawan dan akun siswa</p>
        </div>

        <div class="card p-6">
            <x-alert />

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus class="input" placeholder="nama@sekolah.sch.id">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="label">Password</label>
                    <input type="password" id="password" name="password" required class="input" placeholder="••••••••">
                    @error('password')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    Ingat saya
                </label>
                <button class="btn-primary w-full">Masuk</button>
            </form>
        </div>

        <div class="mt-5 rounded-xl border border-dashed border-slate-300 bg-white p-4 text-sm">
            <p class="font-semibold text-slate-700">Akun demo:</p>
            <div class="mt-2 space-y-1 text-slate-600">
                <p>Pustakawan &mdash; <code class="rounded bg-slate-100 px-1">pustakawan@sekolah.sch.id</code> / <code class="rounded bg-slate-100 px-1">password</code></p>
                <p>Siswa &mdash; <code class="rounded bg-slate-100 px-1">2024001@siswa.sekolah.sch.id</code> / <code class="rounded bg-slate-100 px-1">password</code></p>
            </div>
        </div>

        <p class="mt-6 text-center text-sm text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-primary-600">&larr; Kembali ke beranda</a>
        </p>
    </div>
@endsection
