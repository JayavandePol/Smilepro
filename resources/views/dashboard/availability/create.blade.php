@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-3xl space-y-8">
    <div>
        <p class="text-xs uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400">Nieuwe beschikbaarheid</p>
        <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Beschikbaarheid instellen</h1>
        <p class="text-sm text-slate-600 dark:text-slate-300">Vul alle gegevens in om een nieuwe beschikbaarheid toe te voegen aan het systeem.</p>
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
    <form action="{{ route('dashboard.availability.store') }}" method="POST" class="space-y-6 rounded-3xl border border-slate-200 bg-white px-8 py-8 shadow-xl dark:border-slate-800 dark:bg-slate-900">
    {{-- Requirement 2.1: Responsive form with TailwindCSS --}}
    <form action="{{ route('dashboard.availability.store') }}" method="POST" class="space-y-6 rounded-3xl border border-slate-200 bg-white px-8 py-8 shadow-xl dark:border-slate-800 dark:bg-slate-900">
        @csrf

        {{-- Medewerker --}}
        <div>
            <label for="user_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                Medewerker <span class="text-red-500">*</span>
            </label>
            <select 
                id="user_id" 
                name="user_id" 
                class="mt-2 w-full rounded-xl border px-4 py-3 text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white @error('user_id') border-red-500 dark:border-red-500 @enderror"
                required
            >
                <option value="">Selecteer een medewerker</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ old('user_id') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }} ({{ $employee->email }})
                    </option>
                @endforeach
            </select>
            {{-- Requirement 1.4: validation error messages --}}
            @error('user_id')
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

        {{-- Datum --}}
        <div>
            <label for="available_on" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                Datum <span class="text-red-500">*</span>
            </label>
            <input 
                type="date" 
                id="available_on" 
                name="available_on" 
                value="{{ old('available_on') }}"
                min="{{ date('Y-m-d') }}"
                class="mt-2 w-full rounded-xl border px-4 py-3 text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white @error('available_on') border-red-500 dark:border-red-500 @enderror"
                required
            >
            {{-- Requirement 1.4: validation error messages --}}
            @error('available_on')
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

        {{-- Tijdslot --}}
        <div>
            <label for="slot" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                Tijdstip <span class="text-red-500">*</span>
            </label>
            <select 
                id="slot" 
                name="slot" 
                class="mt-2 w-full rounded-xl border px-4 py-3 text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white @error('slot') border-red-500 dark:border-red-500 @enderror"
                required
            >
                <option value="">Selecteer een tijdstip</option>
                <option value="08:00" {{ old('slot') == '08:00' ? 'selected' : '' }}>08:00 - 09:00</option>
                <option value="09:00" {{ old('slot') == '09:00' ? 'selected' : '' }}>09:00 - 10:00</option>
                <option value="10:00" {{ old('slot') == '10:00' ? 'selected' : '' }}>10:00 - 11:00</option>
                <option value="11:00" {{ old('slot') == '11:00' ? 'selected' : '' }}>11:00 - 12:00</option>
                <option value="12:00" {{ old('slot') == '12:00' ? 'selected' : '' }}>12:00 - 13:00</option>
                <option value="13:00" {{ old('slot') == '13:00' ? 'selected' : '' }}>13:00 - 14:00</option>
                <option value="14:00" {{ old('slot') == '14:00' ? 'selected' : '' }}>14:00 - 15:00</option>
                <option value="15:00" {{ old('slot') == '15:00' ? 'selected' : '' }}>15:00 - 16:00</option>
                <option value="16:00" {{ old('slot') == '16:00' ? 'selected' : '' }}>16:00 - 17:00</option>
                <option value="17:00" {{ old('slot') == '17:00' ? 'selected' : '' }}>17:00 - 18:00</option>
                <option value="18:00" {{ old('slot') == '18:00' ? 'selected' : '' }}>18:00 - 19:00</option>
                <option value="19:00" {{ old('slot') == '19:00' ? 'selected' : '' }}>19:00 - 20:00</option>
                <option value="20:00" {{ old('slot') == '20:00' ? 'selected' : '' }}>20:00 - 21:00</option>
            </select>
            {{-- Requirement 1.4: validation error messages --}}
            @error('slot')
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

        {{-- Status --}}
        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                Status <span class="text-red-500">*</span>
            </label>
            <div class="mt-2 grid grid-cols-3 gap-3">
                <label class="relative flex cursor-pointer flex-col items-center rounded-xl border-2 border-slate-200 bg-slate-50 p-4 transition hover:border-emerald-500 dark:border-slate-700 dark:bg-slate-800 dark:hover:border-emerald-500 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 dark:has-[:checked]:bg-emerald-900/20">
                    <input 
                        type="radio" 
                        name="status" 
                        value="open" 
                        {{ old('status', 'open') == 'open' ? 'checked' : '' }}
                        class="sr-only"
                        required
                    >
                    <svg class="h-6 w-6 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="mt-2 text-sm font-medium text-slate-700 dark:text-slate-300">Open</span>
                </label>

                <label class="relative flex cursor-pointer flex-col items-center rounded-xl border-2 border-slate-200 bg-slate-50 p-4 transition hover:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:hover:border-blue-500 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-900/20">
                    <input 
                        type="radio" 
                        name="status" 
                        value="booked" 
                        {{ old('status') == 'booked' ? 'checked' : '' }}
                        class="sr-only"
                        required
                    >
                    <svg class="h-6 w-6 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="mt-2 text-sm font-medium text-slate-700 dark:text-slate-300">Geboekt</span>
                </label>

                <label class="relative flex cursor-pointer flex-col items-center rounded-xl border-2 border-slate-200 bg-slate-50 p-4 transition hover:border-red-500 dark:border-slate-700 dark:bg-slate-800 dark:hover:border-red-500 has-[:checked]:border-red-500 has-[:checked]:bg-red-50 dark:has-[:checked]:bg-red-900/20">
                    <input 
                        type="radio" 
                        name="status" 
                        value="blocked" 
                        {{ old('status') == 'blocked' ? 'checked' : '' }}
                        class="sr-only"
                        required
                    >
                    <svg class="h-6 w-6 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    <span class="mt-2 text-sm font-medium text-slate-700 dark:text-slate-300">Geblokkeerd</span>
                </label>
            </div>
            {{-- Requirement 1.4: validation error messages --}}
            @error('status')
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

        {{-- Notities --}}
        <div>
            <label for="notes" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                Notities <span class="text-slate-400">(optioneel)</span>
            </label>
            <textarea 
                id="notes" 
                name="notes" 
                rows="4"
                maxlength="500"
                class="mt-2 w-full rounded-xl border px-4 py-3 text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white @error('notes') border-red-500 dark:border-red-500 @enderror"
                placeholder="Eventuele opmerkingen of notities..."
            >{{ old('notes') }}</textarea>
            {{-- Requirement 1.4: validation error messages --}}
            @error('notes')
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Maximaal 500 tekens</p>
        </div>

        {{-- Action buttons --}}
        <div class="flex gap-4 pt-4">
            <button 
                type="submit" 
                class="flex-1 rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white shadow-lg transition hover:bg-blue-500"
            >
                Beschikbaarheid aanmaken
            </button>
            <a 
                href="{{ route('dashboard.availability') }}" 
                class="flex-1 rounded-xl border-2 border-slate-300 bg-white px-6 py-3 text-center font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
            >
                Annuleren
            </a>
        </div>
    </form>
</div>
@endsection
