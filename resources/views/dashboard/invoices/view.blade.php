@extends('layouts.admin')

@section('content')
@php
    $statusLabels = [
        'open' => ['label' => 'Open', 'color' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-200'],
        'betaald' => ['label' => 'Betaald', 'color' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200'],
        'verlopen' => ['label' => 'Verlopen', 'color' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200'],
    ];
@endphp
<div class="space-y-8">
    {{-- Success/Error Flash Messages --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-6 py-4 shadow-sm dark:border-emerald-900/40 dark:bg-emerald-900/20">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-6 py-4 shadow-sm dark:border-red-900/40 dark:bg-red-900/20">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-xs uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400">Facturen</p>
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Financieel overzicht</h1>
            <p class="text-sm text-slate-600 dark:text-slate-300">Stored procedures leveren alle factuurregels inclusief patiëntinfo.</p>
        </div>
        <a href="{{ route('dashboard.invoices.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-blue-700 hover:to-blue-600 transition-all">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nieuwe factuur
        </a>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
        <form method="GET" action="{{ route('dashboard.invoices') }}" class="flex flex-col gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow sm:flex-row sm:items-center dark:border-slate-800 dark:bg-slate-900">
            <label class="text-xs font-semibold uppercase tracking-widest text-slate-400">Status</label>
            <select name="status" class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                <option value="">Alle statussen</option>
                @foreach($statusLabels as $value => $meta)
                    <option value="{{ $value }}" @selected(($activeStatus ?? '') === $value)>{{ $meta['label'] }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-500">Filter</button>
            @if(!empty($activeStatus))
                <a href="{{ route('dashboard.invoices') }}" class="text-xs font-semibold uppercase tracking-wide text-blue-600 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs uppercase tracking-widest text-slate-400">Totaal bedrag</p>
            <p class="mt-2 text-4xl font-semibold text-slate-900 dark:text-white">€ {{ number_format($totals['sum'] ?? 0, 2, ',', '.') }}</p>
            <p class="text-xs text-slate-500">Alle facturen</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 shadow-sm dark:border-amber-500/30 dark:bg-amber-900/30">
            <p class="text-xs uppercase tracking-widest text-amber-700 dark:text-amber-100">Openstaand</p>
            <p class="mt-2 text-4xl font-semibold text-amber-700 dark:text-amber-100">€ {{ number_format($totals['open'] ?? 0, 2, ',', '.') }}</p>
            <p class="text-xs text-amber-700/80 dark:text-amber-100/80">Nog te innen</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 shadow-sm dark:border-emerald-500/30 dark:bg-emerald-900/30">
            <p class="text-xs uppercase tracking-widest text-emerald-700 dark:text-emerald-100">Betaald</p>
            <p class="mt-2 text-4xl font-semibold text-emerald-700 dark:text-emerald-100">€ {{ number_format($totals['paid'] ?? 0, 2, ',', '.') }}</p>
            <p class="text-xs text-emerald-700/80 dark:text-emerald-100/80">Ontvangen</p>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4 text-sm text-slate-500 dark:border-slate-800 dark:text-slate-400">
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                {{ $invoices->count() }} zichtbare facturen
            </div>
            <span>BTW verlegd · {{ now()->format('d-m-Y') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm dark:divide-slate-800">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-widest text-slate-400 dark:bg-slate-800/60 dark:text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Factuurnummer</th>
                        <th class="px-6 py-3">Patiënt</th>
                        <th class="px-6 py-3">Bedrag</th>
                        <th class="px-6 py-3">Uitgiftedatum</th>
                        <th class="px-6 py-3">Vervaldatum</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actie</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white dark:divide-slate-800 dark:bg-slate-900">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">{{ $invoice->invoice_number }}</td>
                            <td class="px-6 py-4">
                                <div class="text-slate-700 dark:text-slate-200">{{ $invoice->patient_name }}</div>
                                <a href="mailto:{{ $invoice->patient_email }}" class="text-xs text-blue-600 hover:underline">{{ $invoice->patient_email }}</a>
                            </td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-200">€ {{ number_format($invoice->total_amount, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $invoice->issue_date?->format('d-m-Y') }}</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $invoice->due_date?->format('d-m-Y') ?? 'n.t.b.' }}</td>
                            <td class="px-6 py-4">
                                @php($color = $statusLabels[$invoice->status]['color'] ?? 'bg-slate-100 text-slate-600')
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $color }}">{{ $statusLabels[$invoice->status]['label'] ?? ucfirst($invoice->status) }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="mailto:{{ $invoice->patient_email }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Stuur herinnering</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">Geen facturen gevonden.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
