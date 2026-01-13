<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Smilepro') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Clash+Display:wght@500;600&display=swap" rel="stylesheet">
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <script>
                tailwind.config = {
                    theme: {
                        extend: {
                            colors: {
                                brand: '#FFB703',
                                brandDark: '#F58216',
                                slateInk: '#0B1120'
                            },
                            boxShadow: {
                                glow: '0 25px 70px rgba(255, 183, 3, 0.18)'
                            }
                        }
                    }
                }
            </script>
        @endif
        <style>
            body {
                font-family: 'Space Grotesk', 'General Sans', 'Inter', sans-serif;
            }
            .title-font {
                font-family: 'Clash Display', 'Space Grotesk', sans-serif;
            }
        </style>
    </head>
    <body class="bg-slate-950 text-white overflow-x-hidden">
        <div class="fixed top-0 left-0 right-0 h-[45vh] pointer-events-none" aria-hidden="true">
            <div class="absolute inset-0 bg-gradient-to-r from-[#172554] via-[#0F172A] to-[#1E293B]"></div>
            <div class="absolute -top-24 right-12 w-72 h-72 rounded-full bg-gradient-to-br from-brand via-brandDark to-transparent opacity-60 blur-3xl"></div>
            <div class="absolute top-10 left-[-120px] w-80 h-80 rounded-full bg-gradient-to-br from-[#0EA5E9] via-transparent to-transparent opacity-60 blur-[120px]"></div>
        </div>

        <div class="relative z-10">
            <header class="max-w-6xl mx-auto px-6 pt-12">
                <nav class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white text-slate-900 flex items-center justify-center text-xl font-semibold shadow-glow">
                            SP
                        </div>
                        <div>
                            <p class="title-font text-xl tracking-tight">{{ config('app.name', 'Smilepro') }}</p>
                            <p class="text-sm text-slate-300">Digitale glimlachbeleving voor moderne praktijken</p>
                        </div>
                    </div>
                    @if (Route::has('login'))
                        <div class="flex flex-wrap items-center gap-3 text-sm font-medium">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="px-4 py-2 rounded-full border border-white/30 hover:border-white transition">Ga naar dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="px-4 py-2 rounded-full border border-white/30 hover:border-white transition">Log in</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-full bg-brand text-slate-950 font-semibold hover:bg-brandDark transition">Word partner</a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </nav>
            </header>

            <main class="max-w-6xl mx-auto px-6 pb-24 pt-12 space-y-24">
                <section class="grid lg:grid-cols-[1.1fr,0.9fr] gap-12 items-center">
                    <div class="space-y-8">
                        <p class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-white/20 text-sm text-slate-200">
                            <span class="w-2 h-2 rounded-full bg-brand"></span>
                            Smile Journey OS v2.4 live
                        </p>
                        <div class="space-y-6">
                            <h1 class="title-font text-4xl md:text-5xl leading-tight">
                                Verander consulten in <span class="text-brand">wow-momenten</span> met Smilepro.
                            </h1>
                            <p class="text-lg text-slate-200 max-w-2xl">
                                Combineer realtime behandeldata, klantcommunicatie en sales coaching in één vloeiend scherm dat je team energie geeft en cliënten vertrouwen biedt.
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <a href="#elevate" class="px-6 py-3 rounded-full bg-brand text-slate-950 font-semibold shadow-glow hover:bg-brandDark transition">Plan een demo</a>
                            <button class="px-6 py-3 rounded-full border border-white/30 hover:border-white transition">Bekijk producttour</button>
                        </div>
                        <div class="grid grid-cols-2 gap-6 text-sm">
                            <div>
                                <p class="text-4xl font-semibold">98%</p>
                                <p class="text-slate-300">tevredenheidsscore op intake-beleving</p>
                            </div>
                            <div>
                                <p class="text-4xl font-semibold">14 dagen</p>
                                <p class="text-slate-300">tot volledig uitgerolde adoptie</p>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="absolute -inset-6 bg-gradient-to-br from-brand/40 via-transparent to-transparent blur-3xl"></div>
                        <div class="relative rounded-3xl bg-white/5 border border-white/10 backdrop-blur-2xl p-8 shadow-2xl space-y-8">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-slate-300">Live vertrouwenmeter</p>
                                    <p class="text-3xl font-semibold text-white">8.7</p>
                                </div>
                                <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-300 text-xs">Realtime</span>
                            </div>
                            <div class="space-y-4">
                                <p class="text-sm text-slate-400">Behandelreis</p>
                                <div class="space-y-4">
                                    @php
                                        $steps = [
                                            ['label' => 'Intake & moodboard', 'progress' => 82],
                                            ['label' => 'Scan & visualisatie', 'progress' => 64],
                                            ['label' => 'Aanbod & commitment', 'progress' => 38],
                                        ];
                                    @endphp
                                    @foreach ($steps as $step)
                                        <div>
                                            <div class="flex justify-between text-xs text-slate-400">
                                                <span>{{ $step['label'] }}</span>
                                                <span>{{ $step['progress'] }}%</span>
                                            </div>
                                            <div class="mt-2 h-2 rounded-full bg-white/10">
                                                <div class="h-full rounded-full bg-gradient-to-r from-brand to-brandDark" style="width: {{ $step['progress'] }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="rounded-2xl bg-slate-900/70 border border-white/5 p-4">
                                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Team pulse</p>
                                <p class="text-4xl font-semibold text-white mt-2">+27%</p>
                                <p class="text-sm text-slate-300">Omzetgroei per stoel sinds activatie</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="elevate" class="space-y-8">
                    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                        <div>
                            <p class="text-brand text-sm font-semibold">Modules</p>
                            <h2 class="title-font text-3xl mt-2">Bouw een georkestreerde glimlachreis</h2>
                        </div>
                        <p class="text-slate-300 max-w-2xl">Combineer emotie en data. Smilepro brengt marketing, frontdesk en behandelkamer samen met duidelijke ritmes, zodat elke stap aanvoelt als persoonlijke hospitality.</p>
                    </div>
                    <div class="grid md:grid-cols-3 gap-6">
                        @php
                            $features = [
                                ['title' => 'Experience Canvas', 'copy' => 'Maak interactieve moodboards en AR-voorbeelden vanuit bestaande dossiers.', 'accent' => 'from-[#FDE68A] to-[#FBBF24]'],
                                ['title' => 'Pulse CRM', 'copy' => 'Volg leads, referrals en herhaalafspraken met voorspellende alerts.', 'accent' => 'from-[#6EE7B7] to-[#10B981]'],
                                ['title' => 'Revenue Playbooks', 'copy' => 'Stuur teams met dagrituelen, video scripts en uniforme opvolging.', 'accent' => 'from-[#93C5FD] to-[#3B82F6]'],
                            ];
                        @endphp
                        @foreach ($features as $feature)
                            <article class="relative rounded-3xl border border-white/10 bg-white/5 p-6 overflow-hidden">
                                <div class="absolute inset-0 opacity-20 bg-gradient-to-br {{ $feature['accent'] }}"></div>
                                <div class="relative space-y-4">
                                    <h3 class="text-xl font-semibold">{{ $feature['title'] }}</h3>
                                    <p class="text-slate-200 text-sm">{{ $feature['copy'] }}</p>
                                    <div class="flex items-center gap-2 text-brand text-sm font-semibold">
                                        <span>Bekijk workflows</span>
                                        <span aria-hidden="true">→</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="grid lg:grid-cols-2 gap-10">
                    <div class="rounded-3xl bg-gradient-to-br from-white/10 to-white/5 border border-white/10 p-8 space-y-6">
                        <p class="text-brand text-sm font-semibold">Vertrouwen</p>
                        <h3 class="title-font text-3xl">Van eerste klik tot nazorg één vloeiende beleving</h3>
                        <ul class="space-y-5 text-slate-200 text-sm">
                            <li class="flex gap-3">
                                <span class="mt-1 h-6 w-6 flex items-center justify-center rounded-full bg-white/10 text-brand text-xs">1</span>
                                Hyperpersoonlijke pre-visit journeys met intake video en stemming-check.
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-1 h-6 w-6 flex items-center justify-center rounded-full bg-white/10 text-brand text-xs">2</span>
                                Chair-side canvas dat scans, trajectplanning en prijsstrategie combineert.
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-1 h-6 w-6 flex items-center justify-center rounded-full bg-white/10 text-brand text-xs">3</span>
                                Automatische aftercare met story-updates, reviews en membership-aanbod.
                            </li>
                        </ul>
                    </div>
                    <div class="rounded-3xl bg-slate-900/60 border border-white/5 p-8 space-y-8">
                        <p class="text-brand text-sm font-semibold">Resultaten</p>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <p class="text-4xl font-semibold text-white">+41%</p>
                                <span class="text-xs text-slate-400">Lead-to-plan conversie</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-4xl font-semibold text-white">-32%</p>
                                <span class="text-xs text-slate-400">No-show rate</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-4xl font-semibold text-white">3x</p>
                                <span class="text-xs text-slate-400">Sneller adviesproces</span>
                            </div>
                        </div>
                        <div class="rounded-2xl bg-white/5 border border-white/10 p-4 space-y-3">
                            <p class="text-sm text-slate-300">“Smilepro voelt als een hospitality layer over onze hele praktijk. Teams weten exact wat de volgende stap is.”</p>
                            <p class="text-sm font-semibold">Dr. Leonie van der Sande — Glow Clinic</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 to-slate-900/70 p-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="space-y-4 max-w-2xl">
                        <p class="text-brand text-sm font-semibold">Aan de slag</p>
                        <h3 class="title-font text-3xl">Activeer Smilepro binnen twee weken met ons field team.</h3>
                        <p class="text-slate-200">We koppelen je huidige PMS-systemen, trainen frontdesk en behandelaren, en leveren een gepersonaliseerde journey kit.</p>
                    </div>
                    <div class="flex flex-col gap-3 w-full max-w-sm">
                        <a href="mailto:hello@smilepro.com" class="px-6 py-4 rounded-2xl bg-brand text-slate-900 font-semibold text-center hover:bg-brandDark transition">Vraag een blueprint aan</a>
                        <p class="text-xs text-center text-slate-400">Of bel ons team via +31 20 123 45 67</p>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
