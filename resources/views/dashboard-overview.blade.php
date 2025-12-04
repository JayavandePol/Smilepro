@extends('layouts.admin')

@section('content')
@php
    $totalUsers = $metrics['total_users'] ?? 0;
    $verifiedUsers = $metrics['verified_users'] ?? 0;
    $managementMembers = $metrics['management_members'] ?? 0;
    $unverifiedUsers = max($totalUsers - $verifiedUsers, 0);
@endphp

<div class="space-y-10">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.4em] text-slate-400">Smilepro</p>
            <h1 class="text-4xl font-semibold text-slate-900 dark:text-white">Dashboard overzicht</h1>
            <p class="text-slate-500 dark:text-slate-300">Een snelle blik op de praktijkactiviteiten.</p>
        </div>
        <div class="inline-flex items-center gap-4 rounded-2xl bg-white/80 px-6 py-4 shadow-lg dark:bg-white/10">
            <div class="text-left">
                <p class="text-xs uppercase text-slate-500">Ingelogd als</p>
                <p class="text-lg font-semibold text-slate-900 dark:text-white">{{ $user->name }}</p>
            </div>
            <span class="rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-600">Actief</span>
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-100 bg-white/90 p-6 shadow-xl dark:border-white/10 dark:bg-slate-900/60">
            <p class="text-sm text-slate-500">Vandaag</p>
            <p class="mt-2 text-3xl font-semibold text-slate-900 dark:text-white">{{ $totalUsers }}</p>
            <p class="mt-1 text-xs text-slate-400">Totaal geregistreerde gebruikers</p>
        </div>
        <div class="rounded-3xl border border-slate-100 bg-white/90 p-6 shadow-xl dark:border-white/10 dark:bg-slate-900/60">
            <p class="text-sm text-slate-500">Verifieerd</p>
            <p class="mt-2 text-3xl font-semibold text-slate-900 dark:text-white">{{ $verifiedUsers }}</p>
            <p class="mt-1 text-xs text-slate-400">Gebruikers met bevestigd e-mailadres</p>
        </div>
        <div class="rounded-3xl border border-slate-100 bg-white/90 p-6 shadow-xl dark:border-white/10 dark:bg-slate-900/60">
            <p class="text-sm text-slate-500">Nog te verifiëren</p>
            <p class="mt-2 text-3xl font-semibold text-slate-900 dark:text-white">{{ $unverifiedUsers }}</p>
            <p class="mt-1 text-xs text-slate-400">Accounts zonder verificatie</p>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-100 bg-gradient-to-br from-white to-slate-50 p-8 shadow-2xl dark:border-white/5 dark:from-slate-900 dark:to-slate-950">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Belangrijke meldingen</h2>
                <p class="text-slate-500 dark:text-slate-300">Gebruik deze ruimte voor alerts, nieuws of notities.</p>
            </div>
            <a href="{{ route('dashboard.users') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-5 py-2 text-sm font-semibold text-slate-600 hover:bg-white dark:border-white/10 dark:text-white dark:hover:bg-white/10">
                Ga naar gebruikersbeheer
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <div class="rounded-2xl border border-white/40 bg-white/80 p-4 dark:border-white/10 dark:bg-white/5">
                <p class="text-xs uppercase text-slate-500">Praktijkmanagement</p>
                <p class="mt-2 text-slate-700 dark:text-slate-100">{{ $managementMembers }} actieve leden met managementrechten.</p>
            </div>
            <div class="rounded-2xl border border-white/40 bg-white/80 p-4 dark:border-white/10 dark:bg-white/5">
                <p class="text-xs uppercase text-slate-500">Nieuwe accounts</p>
                <p class="mt-2 text-slate-700 dark:text-slate-100">Gebruik deze kaart om snel meldingen te tonen over recente aanmeldingen.</p>
            </div>
        </div>
    </div>
</div>
@endsection
