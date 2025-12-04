@extends('layouts.admin')

{{-- Requirement 2.1: Responsive grid/table powered by Tailwind just like the users overview. --}}
@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm uppercase tracking-widest text-gray-500 dark:text-gray-400">team</p>
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-gray-100">Medewerker overzicht</h1>
            <p class="text-gray-600 dark:text-gray-300">Bekijk alle medewerkers per rol en filter op specialisaties.</p>
        </div>
        <div class="inline-flex flex-wrap gap-3">
            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 dark:border-emerald-400/30 dark:bg-emerald-900/20 dark:text-emerald-200">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                {{ $users->count() }} medewerkers
            </span>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 flex flex-wrap gap-4 items-center justify-between border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <span class="h-2 w-2 rounded-full bg-blue-400"></span>
                Rollen: tandarts · mondhygienist · assistent · praktijkmanagement
            </div>
            <form method="GET" action="{{ route('dashboard.employees') }}" class="flex flex-wrap items-center gap-3">
                <label class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">Filter op rol</label>
                <select name="role" class="rounded-full border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    <option value="">Alle rollen</option>
                    @foreach(($roleOptions ?? collect()) as $role)
                        <option value="{{ $role }}" @selected(($activeRole ?? '') === $role)>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-full bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-blue-500">Filter</button>
                @if(!empty($activeRole))
                    <a href="{{ route('dashboard.employees') }}" class="rounded-full border border-gray-300 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Naam</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">E-mail</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Rol(len)</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">In dienst sinds</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Contact</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-800">
                    @forelse($users as $employee)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-cyan-500 text-white flex items-center justify-center font-semibold">
                                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">ID: #{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $employee->email }}</td>
                        <td class="px-6 py-4 text-sm">
                            @php($roleNames = $employee->role_names ?? [])
                            <div class="flex flex-wrap gap-2">
                                @forelse($roleNames as $role)
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200">{{ $role }}</span>
                                @empty
                                    <span class="text-gray-400">Geen rol</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $employee->created_at?->format('d-m-Y') ?? 'Onbekend' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="mailto:{{ $employee->email }}" class="inline-flex items-center gap-2 rounded-full border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                                Stuur mail
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16v16H4z" opacity="0.2" />
                                    <path d="M22 6l-10 7L2 6" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                            Nog geen medewerkers gevonden voor deze filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
