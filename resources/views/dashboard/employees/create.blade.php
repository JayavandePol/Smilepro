@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-3xl space-y-8">
    <div>
        <p class="text-xs uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400">Nieuwe medewerker</p>
        <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Medewerker aanmaken</h1>
        <p class="text-sm text-slate-600 dark:text-slate-300">Vul alle gegevens in om een nieuwe medewerker toe te voegen aan het systeem.</p>
    </div>

    {{-- Requirement 1.2: Success feedback --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm text-emerald-800 shadow dark:border-emerald-500/30 dark:bg-emerald-900/30 dark:text-emerald-100">
            {{ session('success') }}
        </div>
    @endif

    {{-- Requirement 1.4: Error feedback (unhappy scenario) --}}
    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-6 py-4 text-sm text-red-800 shadow dark:border-red-500/30 dark:bg-red-900/30 dark:text-red-100">
            {{ session('error') }}
        </div>
    @endif

    {{-- Requirement 2.1: Responsive form with TailwindCSS --}}
    <form action="{{ route('dashboard.employees.store') }}" method="POST" class="space-y-6 rounded-3xl border border-slate-200 bg-white px-8 py-8 shadow-xl dark:border-slate-800 dark:bg-slate-900">
        @csrf

        {{-- Naam --}}
        <div>
            <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                Naam <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name') }}"
                class="mt-2 w-full rounded-xl border px-4 py-3 text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white @error('name') border-red-500 dark:border-red-500 @enderror"
                required
            >
            {{-- Requirement 1.4: validation error messages --}}
            @error('name')
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

        {{-- E-mailadres --}}
        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                E-mailadres <span class="text-red-500">*</span>
            </label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}"
                class="mt-2 w-full rounded-xl border px-4 py-3 text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white @error('email') border-red-500 dark:border-red-500 @enderror"
                required
            >
            {{-- Requirement 1.4: validation error messages --}}
            @error('email')
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

        {{-- Wachtwoord --}}
        <div>
            <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                Wachtwoord <span class="text-red-500">*</span>
            </label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                class="mt-2 w-full rounded-xl border px-4 py-3 text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white @error('password') border-red-500 dark:border-red-500 @enderror"
                required
            >
            {{-- Requirement 1.4: validation error messages --}}
            @error('password')
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Minimaal 8 tekens</p>
        </div>

        {{-- Wachtwoord bevestigen --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                Bevestig wachtwoord <span class="text-red-500">*</span>
            </label>
            <input 
                type="password" 
                id="password_confirmation" 
                name="password_confirmation" 
                class="mt-2 w-full rounded-xl border px-4 py-3 text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                required
            >
        </div>

        {{-- Rol --}}
        <div>
            <label for="role" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                Rol <span class="text-red-500">*</span>
            </label>
            <select 
                id="role" 
                name="role" 
                class="mt-2 w-full rounded-xl border px-4 py-3 text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white @error('role') border-red-500 dark:border-red-500 @enderror"
                required
            >
                <option value="">Selecteer een rol</option>
                <option value="tandarts" {{ old('role') == 'tandarts' ? 'selected' : '' }}>Tandarts</option>
                <option value="mondhygienist" {{ old('role') == 'mondhygienist' ? 'selected' : '' }}>Mondhygiënist</option>
                <option value="assistent" {{ old('role') == 'assistent' ? 'selected' : '' }}>Assistent</option>
                <option value="praktijkmanagement" {{ old('role') == 'praktijkmanagement' ? 'selected' : '' }}>Praktijkmanagement</option>
            </select>
            {{-- Requirement 1.4: validation error messages --}}
            @error('role')
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

        {{-- Action buttons --}}
        <div class="flex gap-4 pt-4">
            <button 
                type="submit" 
                class="flex-1 rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white shadow-lg transition hover:bg-blue-500"
            >
                Medewerker aanmaken
            </button>
            <a 
                href="{{ route('dashboard.employees') }}" 
                class="flex-1 rounded-xl border-2 border-slate-300 bg-white px-6 py-3 text-center font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
            >
                Annuleren
            </a>
        </div>
    </form>
</div>
@endsection
