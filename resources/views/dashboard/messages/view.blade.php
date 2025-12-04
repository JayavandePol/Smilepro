@extends('layouts.admin')

@section('content')
@php
    $statusMeta = [
        'nieuw' => ['label' => 'Nieuw', 'dot' => 'bg-blue-500'],
        'gelezen' => ['label' => 'Gelezen', 'dot' => 'bg-amber-500'],
        'opgelost' => ['label' => 'Opgelost', 'dot' => 'bg-emerald-500'],
    ];
@endphp
<div class="space-y-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-xs uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400">Berichten</p>
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Communicatiecentrum</h1>
            <p class="text-sm text-slate-600 dark:text-slate-300">Ingekomen patiëntvragen via stored procedure & fallback joins.</p>
        </div>
        <form method="GET" action="{{ route('dashboard.messages') }}" class="flex flex-col gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow sm:flex-row sm:items-center dark:border-slate-800 dark:bg-slate-900">
            <label class="text-xs font-semibold uppercase tracking-widest text-slate-400">Status</label>
            <select name="status" class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                <option value="">Alles</option>
                @foreach($statusMeta as $value => $meta)
                    <option value="{{ $value }}" @selected(($activeStatus ?? '') === $value)>{{ $meta['label'] }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-500">Filter</button>
            @if(!empty($activeStatus))
                <a href="{{ route('dashboard.messages') }}" class="text-xs font-semibold uppercase tracking-wide text-blue-600 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        @foreach($statusMeta as $key => $meta)
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center gap-2 text-xs uppercase tracking-widest text-slate-400">
                    <span class="h-2 w-2 rounded-full {{ $meta['dot'] }}"></span>
                    {{ $meta['label'] }}
                </div>
                <p class="mt-2 text-4xl font-semibold text-slate-900 dark:text-white">{{ $statusCounts[$key] ?? 0 }}</p>
                <p class="text-xs text-slate-500">Tickets</p>
            </div>
        @endforeach
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4 text-sm text-slate-500 dark:border-slate-800 dark:text-slate-400">
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-blue-400"></span>
                {{ $messages->count() }} zichtbare berichten
            </div>
            <span>Laatste sync: {{ now()->format('H:i') }}</span>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($messages as $message)
                <article class="flex flex-col gap-3 px-6 py-5 transition hover:bg-slate-50 dark:hover:bg-slate-800/60 md:flex-row md:items-center md:justify-between">
                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $message->subject }}</p>
                        <p class="text-xs text-slate-400">{{ $message->patient_name }} · <a href="mailto:{{ $message->patient_email }}" class="text-blue-500 hover:underline">{{ $message->patient_email }}</a></p>
                        <p class="text-sm text-slate-600 dark:text-slate-300">{{ \Illuminate\Support\Str::limit($message->body ?? 'Geen berichttekst', 140) }}</p>
                    </div>
                    <div class="flex flex-col items-start gap-3 sm:flex-row sm:items-center">
                        @php($color = $statusMeta[$message->status]['dot'] ?? 'bg-slate-400')
                        <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-600 dark:border-slate-700 dark:text-slate-200">
                            <span class="h-2 w-2 rounded-full {{ $color }}"></span>
                            {{ $statusMeta[$message->status]['label'] ?? ucfirst($message->status) }}
                        </span>
                        <span class="text-xs text-slate-500 dark:text-slate-400">Ontvangen {{ $message->received_at?->diffForHumans() }}</span>
                        <a href="mailto:{{ $message->patient_email }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Reageer</a>
                    </div>
                </article>
            @empty
                <p class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">Geen berichten gevonden.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
