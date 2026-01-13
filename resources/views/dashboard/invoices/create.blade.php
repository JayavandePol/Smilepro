@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-3xl space-y-8">
    <div>
        <p class="text-xs uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400">Nieuwe factuur</p>
        <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Factuur aanmaken</h1>
        <p class="text-sm text-slate-600 dark:text-slate-300">Vul alle gegevens in om een nieuwe factuur toe te voegen aan het systeem.</p>
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
    <form action="{{ route('dashboard.invoices.store') }}" method="POST" class="space-y-6 rounded-3xl border border-slate-200 bg-white px-8 py-8 shadow-xl dark:border-slate-800 dark:bg-slate-900">
        @csrf

        {{-- Patiënt --}}
        <div>
            <label for="patient_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                Patiënt <span class="text-red-500">*</span>
            </label>
            <select 
                id="patient_id" 
                name="patient_id" 
                class="mt-2 w-full rounded-xl border px-4 py-3 text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white @error('patient_id') border-red-500 dark:border-red-500 @enderror"
                required
            >
                <option value="">Selecteer een patiënt</option>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                        {{ $patient->last_name }}, {{ $patient->first_name }} ({{ $patient->email }})
                    </option>
                @endforeach
            </select>
            {{-- Requirement 1.4: validation error messages --}}
            @error('patient_id')
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

        {{-- Totaalbedrag --}}
        <div>
            <label for="total_amount" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                Totaalbedrag (€) <span class="text-red-500">*</span>
            </label>
            <input 
                type="number" 
                id="total_amount" 
                name="total_amount" 
                value="{{ old('total_amount') }}"
                step="0.01"
                min="0.01"
                max="99999.99"
                class="mt-2 w-full rounded-xl border px-4 py-3 text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white @error('total_amount') border-red-500 dark:border-red-500 @enderror"
                required
            >
            {{-- Requirement 1.4: validation error messages --}}
            @error('total_amount')
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

        {{-- Factuurdatum --}}
        <div>
            <label for="issue_date" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                Factuurdatum <span class="text-red-500">*</span>
            </label>
            <input 
                type="date" 
                id="issue_date" 
                name="issue_date" 
                value="{{ old('issue_date', date('Y-m-d')) }}"
                class="mt-2 w-full rounded-xl border px-4 py-3 text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white @error('issue_date') border-red-500 dark:border-red-500 @enderror"
                required
            >
            {{-- Requirement 1.4: validation error messages --}}
            @error('issue_date')
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

        {{-- Vervaldatum --}}
        <div>
            <label for="due_date" class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                Vervaldatum <span class="text-slate-400">(optioneel)</span>
            </label>
            <input 
                type="date" 
                id="due_date" 
                name="due_date" 
                value="{{ old('due_date') }}"
                class="mt-2 w-full rounded-xl border px-4 py-3 text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white @error('due_date') border-red-500 dark:border-red-500 @enderror"
            >
            {{-- Requirement 1.4: validation error messages --}}
            @error('due_date')
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

        {{-- Status --}}
        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">
                Status <span class="text-red-500">*</span>
            </label>
            <div class="mt-2 grid grid-cols-3 gap-3">
                <label class="relative flex cursor-pointer flex-col items-center rounded-xl border-2 border-slate-200 bg-slate-50 p-4 transition hover:border-amber-500 dark:border-slate-700 dark:bg-slate-800 dark:hover:border-amber-500 has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 dark:has-[:checked]:bg-amber-900/20">
                    <input 
                        type="radio" 
                        name="status" 
                        value="open" 
                        {{ old('status', 'open') == 'open' ? 'checked' : '' }}
                        class="sr-only"
                        required
                    >
                    <svg class="h-6 w-6 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="mt-2 text-sm font-medium text-slate-700 dark:text-slate-300">Open</span>
                </label>

                <label class="relative flex cursor-pointer flex-col items-center rounded-xl border-2 border-slate-200 bg-slate-50 p-4 transition hover:border-emerald-500 dark:border-slate-700 dark:bg-slate-800 dark:hover:border-emerald-500 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 dark:has-[:checked]:bg-emerald-900/20">
                    <input 
                        type="radio" 
                        name="status" 
                        value="betaald" 
                        {{ old('status') == 'betaald' ? 'checked' : '' }}
                        class="sr-only"
                        required
                    >
                    <svg class="h-6 w-6 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="mt-2 text-sm font-medium text-slate-700 dark:text-slate-300">Betaald</span>
                </label>

                <label class="relative flex cursor-pointer flex-col items-center rounded-xl border-2 border-slate-200 bg-slate-50 p-4 transition hover:border-red-500 dark:border-slate-700 dark:bg-slate-800 dark:hover:border-red-500 has-[:checked]:border-red-500 has-[:checked]:bg-red-50 dark:has-[:checked]:bg-red-900/20">
                    <input 
                        type="radio" 
                        name="status" 
                        value="verlopen" 
                        {{ old('status') == 'verlopen' ? 'checked' : '' }}
                        class="sr-only"
                        required
                    >
                    <svg class="h-6 w-6 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="mt-2 text-sm font-medium text-slate-700 dark:text-slate-300">Verlopen</span>
                </label>
            </div>
            {{-- Requirement 1.4: validation error messages --}}
            @error('status')
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

        {{-- Info box --}}
        <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 dark:border-blue-900/40 dark:bg-blue-900/20">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-blue-800 dark:text-blue-200">Het factuurnummer wordt automatisch gegenereerd in het formaat INV-YYYYMMDD-XXXX</p>
            </div>
        </div>

        {{-- Action buttons --}}
        <div class="flex gap-4 pt-4">
            <button 
                type="submit" 
                class="flex-1 rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white shadow-lg transition hover:bg-blue-500"
            >
                Factuur aanmaken
            </button>
            <a 
                href="{{ route('dashboard.invoices') }}" 
                class="flex-1 rounded-xl border-2 border-slate-300 bg-white px-6 py-3 text-center font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
            >
                Annuleren
            </a>
        </div>
    </form>
</div>
@endsection
