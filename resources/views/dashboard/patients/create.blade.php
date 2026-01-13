@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-3xl space-y-8">
    <div>
        <p class="text-xs uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400">Nieuwe patiënt</p>
        <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Patiënt aanmaken</h1>
        <p class="text-sm text-slate-600 dark:text-slate-300">Vul alle gegevens in om een nieuwe patiënt toe te voegen aan het systeem.</p>
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
    <form action="{{ route('dashboard.patients.store') }}" method="POST" class="space-y-6 rounded-3xl border border-slate-200 bg-white px-8 py-8 shadow-xl dark:border-slate-800 dark:bg-slate-900">
        @csrf

        {{-- Voornaam --}}
        <div>
            <label for="first_name" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                Voornaam <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                id="first_name" 
                name="first_name" 
                value="{{ old('first_name') }}"
                class="mt-2 w-full rounded-xl border px-4 py-3 text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white @error('first_name') border-red-500 dark:border-red-500 @enderror"
                required
            >
            {{-- Requirement 1.4: validation error messages --}}
            @error('first_name')
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

        {{-- Achternaam --}}
        <div>
            <label for="last_name" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                Achternaam <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                id="last_name" 
                name="last_name" 
                value="{{ old('last_name') }}"
                class="mt-2 w-full rounded-xl border px-4 py-3 text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white @error('last_name') border-red-500 dark:border-red-500 @enderror"
                required
            >
            @error('last_name')
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email (unique validation for unhappy scenario) --}}
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
            {{-- Requirement 1.3 & 1.4: Unhappy scenario – email already in use --}}
            @error('email')
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

        {{-- Telefoonnummer --}}
        <div>
            <label for="phone" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                Telefoonnummer
            </label>
            <input 
                type="text" 
                id="phone" 
                name="phone" 
                value="{{ old('phone') }}"
                class="mt-2 w-full rounded-xl border px-4 py-3 text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white @error('phone') border-red-500 dark:border-red-500 @enderror"
            >
            @error('phone')
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

        {{-- Geboortedatum --}}
        <div>
            <label for="date_of_birth" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                Geboortedatum
            </label>
            <input 
                type="date" 
                id="date_of_birth" 
                name="date_of_birth" 
                value="{{ old('date_of_birth') }}"
                class="mt-2 w-full rounded-xl border px-4 py-3 text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white @error('date_of_birth') border-red-500 dark:border-red-500 @enderror"
            >
            @error('date_of_birth')
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

        {{-- Submit buttons --}}
        <div class="flex flex-wrap items-center gap-3 pt-4">
            <button 
                type="submit" 
                class="rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white shadow hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                Patiënt opslaan
            </button>
            <a 
                href="{{ route('dashboard.patients') }}" 
                class="rounded-xl border border-slate-300 bg-white px-6 py-3 font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
            >
                Annuleren
            </a>
        </div>
    </form>
</div>
@endsection
