@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.4em] text-slate-400">Welkom</p>
            <h1 class="text-4xl font-semibold text-slate-900 dark:text-white">Rollenbeheer</h1>
            <p class="text-slate-500 dark:text-slate-300">Beheer snel de rechten binnen de praktijk.</p>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-2xl bg-white/80 p-4 shadow dark:bg-white/10">
                <p class="text-xs uppercase text-slate-500">Totaal gebruikers</p>
                <p class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $users->count() }}</p>
            </div>
            <div class="rounded-2xl bg-white/80 p-4 shadow dark:bg-white/10">
                <p class="text-xs uppercase text-slate-500">Rollen</p>
                <p class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $roles->count() }}</p>
            </div>
        </div>
    </div>

    @if($user->hasRole('praktijkmanagement'))
    <div class="rounded-3xl border border-slate-200 bg-white/90 shadow-xl dark:border-white/10 dark:bg-slate-900/60">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-white/5">
            <div>
                <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Rollen toewijzen</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Klik op "Toewijzen" na het selecteren van een rol.</p>
            </div>
            <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-500">Rechten actief</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-white/10">
                <thead class="bg-slate-50/80 dark:bg-white/5">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Naam</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Huidige rol</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Nieuwe rol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white/70 dark:divide-white/5 dark:bg-slate-900/30">
                    @foreach($users as $managed)
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5">
                        <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white">{{ $managed->name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-200">{{ $managed->email }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/20 dark:text-blue-100">
                                {{ $managed->getRoleNames()->first() ?? 'Geen rol' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('dashboard.roles.assign', $managed) }}" class="flex items-center gap-2">
                                @csrf
                                <select name="role" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring dark:border-white/10 dark:bg-slate-900 dark:text-white">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" @selected($managed->hasRole($role->name))>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow hover:bg-blue-500">
                                    Toewijzen
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
