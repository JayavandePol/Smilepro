@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm uppercase tracking-widest text-gray-500 dark:text-gray-400">beheer</p>
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-gray-100">Gebruikersbeheer</h1>
            <p class="text-gray-600 dark:text-gray-300">Integreer roltoewijzing en gebruikersinzichten op één pagina.</p>
        </div>
        <div class="inline-flex gap-3">
            <button class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700">
                <span class="text-lg leading-none">＋</span>
                Gebruiker toevoegen
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 flex flex-wrap gap-4 items-center justify-between border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                Alleen praktijkmanagement kan deze pagina zien
            </div>
            <div class="flex gap-3">
                <button class="rounded-full border border-gray-300 dark:border-gray-600 px-4 py-1 text-sm text-gray-600 dark:text-gray-300">Alle rollen</button>
                <button class="rounded-full border border-transparent px-4 py-1 text-sm text-gray-500 dark:text-gray-400" disabled>Filters (binnenkort)</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Naam</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">E-mail</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Rol(len)</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Geregistreerd op</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nieuwe rol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-800">
                    @forelse($users as $managedUser)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 text-white flex items-center justify-center font-semibold">
                                    {{ strtoupper(substr($managedUser->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $managedUser->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">ID: #{{ str_pad($managedUser->id, 4, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $managedUser->email }}</td>
                        <td class="px-6 py-4 text-sm">
                            @php($roleNames = $managedUser->role_names ?? [])
                            @if(empty($roleNames))
                                <span class="text-gray-400">Geen rol</span>
                            @else
                                <div class="flex flex-wrap gap-2">
                                    @foreach($roleNames as $role)
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200">{{ $role }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $managedUser->created_at?->format('d-m-Y') ?? 'Onbekend' }}
                        </td>
                        <td class="px-6 py-4">
                            @if(($roles ?? collect())->isNotEmpty())
                                <form method="POST" action="{{ route('dashboard.users.assignRole', $managedUser->id) }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                    @csrf
                                    <select name="role" class="rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" @selected(in_array($role->name, $roleNames ?? []))>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow hover:bg-blue-500">
                                        Toewijzen
                                    </button>
                                </form>
                            @else
                                <p class="text-sm text-gray-400 dark:text-gray-500">Geen rollen beschikbaar.</p>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                            Nog geen gebruikers gevonden.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
