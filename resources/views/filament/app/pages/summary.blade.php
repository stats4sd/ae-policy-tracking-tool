@php
    $pct = fn ($year) => round(($year - $timeline['from']) / ($timeline['to'] - $timeline['from']) * 100, 2);
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        <x-dashboard.mock-banner>
            Per-assessment summary using verified extracts only. Numbers are illustrative and use the proposed −2 to +4 scale, scored per extract × priority action.
        </x-dashboard.mock-banner>

        {{-- Header --}}
        <div class="flex flex-wrap items-baseline justify-between gap-2">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ $assessment['title'] }}</h2>
                <p class="text-sm text-gray-600">
                    {{ $assessment['jurisdiction'] }} ({{ $assessment['jurisdiction_type'] }})
                    · {{ $documents->count() }} policy documents ({{ $stats['oldestYear'] }}–{{ $stats['newestYear'] }})
                    · {{ $assessment['language'] }}
                </p>
            </div>
            <x-filament::badge color="warning" size="lg">Review: shared with partners</x-filament::badge>
        </div>

        {{-- Overview --}}
        <div class="grid gap-6 lg:grid-cols-3">
            <x-filament::section class="header-dark dash-section lg:col-span-2" heading="OVERVIEW">
                @if($prep)
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500" for="summary-text">Assessment summary (shown to partners)</label>
                    <textarea id="summary-text" rows="4" class="w-full border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">{{ $assessment['summary'] }}</textarea>
                @else
                    <p class="text-sm leading-relaxed text-gray-800">{{ $assessment['summary'] }}</p>
                @endif
            </x-filament::section>
            <div class="grid grid-cols-2 gap-4">
                <x-dashboard.stat :value="$stats['evidenced']" :of="$stats['total']" label="priority actions with evidence" :percent="round($stats['evidenced'] / $stats['total'] * 100)"/>
                <x-dashboard.stat :value="$stats['supportive']" label="mostly supportive"/>
                <x-dashboard.stat :value="$stats['perverse']" label="with perverse incentives"/>
                <x-dashboard.stat :value="$stats['medianYear']" label="median policy year"/>
            </div>
        </div>

        {{-- Key findings --}}
        <x-filament::section class="header-dark dash-section" heading="KEY FINDINGS" :description="$prep ? 'Star the statements to show on the summary. In the real page this lists every statement, filterable by priority action.' : 'Selected by the assessment team'">
            @if($prep)
                <ul class="divide-y divide-gray-100">
                    @foreach($keyFindings as $index => $finding)
                        <li class="flex items-start gap-3 py-3" wire:key="prep-{{ $index }}">
                            <button type="button" wire:click="toggleFeatured({{ $index }})" class="mt-0.5" title="Toggle featured">
                                <x-filament::icon
                                    :icon="in_array($index, $featured) ? 'heroicon-s-star' : 'heroicon-o-star'"
                                    :class="in_array($index, $featured) ? 'h-5 w-5 text-warning-500' : 'h-5 w-5 text-gray-400'"/>
                            </button>
                            <div class="min-w-0 flex-1 text-sm">
                                <p class="text-gray-800">{{ $finding['text'] }}</p>
                                <p class="mt-1 font-mono text-xs text-gray-500">PA {{ $finding['pa'] }} · {{ $finding['extracts'] }} extracts · scores {{ $finding['range'][0] }} to +{{ $finding['range'][1] }}</p>
                            </div>
                            <x-filament::icon icon="heroicon-o-bars-3" class="h-5 w-5 text-gray-300" title="Drag to reorder"/>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="grid gap-4 md:grid-cols-3">
                    @foreach(collect($keyFindings)->only($featured) as $finding)
                        @php($edge = $finding['range'][0] < 0 && $finding['range'][1] <= 0 ? 'n2' : ($finding['range'][1] >= 4 ? '4' : '3'))
                        <div class="border-l-4 pl-3 text-sm" style="border-color: var(--score-{{ $edge }})">
                            <p class="font-mono text-xs text-gray-500">PA {{ $finding['pa'] }} · {{ $finding['pa_name'] }}</p>
                            <p class="mt-1 text-gray-800">{{ $finding['text'] }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ $finding['extracts'] }} supporting extracts</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-filament::section>

        {{-- Priority action grid --}}
        <x-filament::section class="header-dark dash-section" heading="PRIORITY ACTIONS" description="Score mix of verified extracts for each priority action. Select one to see the detail.">
            <div class="space-y-3 overflow-x-auto">
                @foreach($recommendations as $recId => $recName)
                    <div class="grid min-w-[640px] grid-cols-[11rem_1fr] items-center gap-3">
                        <div class="text-sm">
                            <span class="font-mono text-xs text-gray-500">Rec {{ $recId }}</span>
                            <div class="font-medium text-gray-800">{{ $recName }}</div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($priorityActions[$recId] as $pa)
                                <button type="button"
                                        wire:click="selectPa('{{ $pa['id'] }}')"
                                        title="PA {{ $pa['id'] }}: {{ $pa['short_name'] }}"
                                        @class([
                                            'flex h-16 w-24 flex-col justify-between border p-1.5 text-left',
                                            'border-gray-200 bg-white hover:border-primary-400' => $pa['extracts'] > 0,
                                            'dash-gap-cell border-gray-200' => $pa['extracts'] === 0,
                                            'ring-2 ring-primary-600 ring-offset-1' => $pa['id'] === $selected['id'],
                                        ])>
                                    <span class="flex items-center justify-between font-mono text-xs">
                                        {{ $pa['id'] }}
                                        <x-dashboard.direction :direction="$pa['direction']"/>
                                    </span>
                                    @if($pa['extracts'] > 0)
                                        <x-dashboard.score-bar :dist="$pa['dist']" height="h-3"/>
                                        <span class="text-[10px] text-gray-500">{{ $pa['docs'] }} {{ Str::plural('doc', $pa['docs']) }}</span>
                                    @else
                                        <span class="text-[10px] text-gray-500">no evidence</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 space-y-1 border-t border-gray-100 pt-3">
                <x-dashboard.score-legend :scores="$scores"/>
                <p class="text-xs text-gray-500">▲ mostly supportive · ▼ mostly perverse incentives · ◆ mixed (25% or more on both sides). The direction rule is a placeholder for the team to agree.</p>
            </div>
        </x-filament::section>

        {{-- Drill-down --}}
        <x-filament::section class="header-dark dash-section" :heading="'PRIORITY ACTION '.$selected['id']" :description="$selected['short_name']">
            @if($selected['extracts'] === 0)
                <p class="text-sm text-gray-600">No verified evidence was found for this priority action in the documents reviewed. This is a policy gap for the assessment.</p>
            @else
                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="space-y-4">
                        <div>
                            <p class="mb-1 text-xs font-semibold uppercase text-gray-500">All documents · {{ $selected['extracts'] }} verified extracts</p>
                            <x-dashboard.score-bar :dist="$selected['dist']" height="h-5"/>
                            <div class="mt-2 grid grid-cols-7 gap-1 text-center">
                                @foreach($selected['dist'] as $score => $count)
                                    <div>
                                        <x-dashboard.score-chip :score="$score" class="h-5 w-full"/>
                                        <div class="mt-0.5 text-xs tabular-nums text-gray-600">{{ $count }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if($drillDown)
                            <div>
                                <p class="mb-2 text-xs font-semibold uppercase text-gray-500">By document</p>
                                <div class="space-y-2">
                                    @foreach($drillDown['by_document'] as $doc)
                                        <div class="grid grid-cols-[12rem_1fr_3rem] items-center gap-2 text-sm">
                                            <span class="truncate text-gray-700" title="{{ $doc['name'] }}">{{ $doc['name'] }}</span>
                                            <x-dashboard.score-bar :dist="$doc['dist']" height="h-3"/>
                                            <span class="text-right text-xs tabular-nums text-gray-500">{{ array_sum($doc['dist']) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="mb-2 text-xs font-semibold uppercase text-gray-500">Statements by theme</p>
                        @if($drillDown)
                            <div class="space-y-3">
                                @foreach($drillDown['statements'] as $statement)
                                    <div class="border border-gray-200 p-3 text-sm">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-xs font-semibold text-gray-600">{{ $statement['theme'] }}</span>
                                            <span class="flex items-center gap-1">
                                                <x-dashboard.score-chip :score="$statement['range'][0]" class="h-5 w-7"/>
                                                <span class="text-xs text-gray-400">to</span>
                                                <x-dashboard.score-chip :score="$statement['range'][1]" class="h-5 w-7"/>
                                            </span>
                                        </div>
                                        <p class="mt-1 text-gray-800">{{ $statement['text'] }}</p>
                                        <x-filament::link href="#" size="sm" class="mt-1">Show {{ $statement['extracts'] }} extracts</x-filament::link>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">The mockup only has example statements and per-document detail for PA 1.2.</p>
                        @endif
                    </div>
                </div>
            @endif
        </x-filament::section>

        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Policy timeline --}}
            <x-filament::section class="header-dark dash-section" heading="POLICY DOCUMENTS OVER TIME" description="How current is the policy framework that was reviewed?">
                <div class="space-y-2">
                    @foreach($documents as $doc)
                        @php($end = $doc['end_year'] ?? $timeline['today'])
                        <div class="grid grid-cols-[11rem_1fr] items-center gap-2 text-sm">
                            <span class="truncate text-gray-700" title="{{ $doc['name'] }}">{{ $doc['name'] }}</span>
                            <div class="relative h-3 bg-gray-100">
                                <div @class(['absolute inset-y-0', 'bg-primary-600' => $doc['end_year'] === null || $doc['end_year'] >= $timeline['assessmentYear'], 'bg-gray-400' => $doc['end_year'] !== null && $doc['end_year'] < $timeline['assessmentYear']])
                                     style="left: {{ $pct($doc['year']) }}%; width: {{ $pct($end) - $pct($doc['year']) }}%"
                                     title="{{ $doc['year'] }}–{{ $doc['end_year'] ?? 'ongoing' }}"></div>
                                <div class="absolute -inset-y-1 w-0.5 bg-orange-title" style="left: {{ $pct($timeline['assessmentYear']) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                    <div class="grid grid-cols-[11rem_1fr] gap-2">
                        <span></span>
                        <div class="flex justify-between font-mono text-[10px] text-gray-500">
                            @foreach([1995, 2005, 2015, 2025] as $tick)
                                <span>{{ $tick }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <p class="mt-3 text-xs text-gray-500">Green: in force at the assessment year (orange line). Grey: expired. Open-ended policies run to today.</p>
            </x-filament::section>

            {{-- AE principles --}}
            <x-filament::section class="header-dark dash-section" heading="AGROECOLOGY PRINCIPLES" description="Approximation: scores of the priority actions linked to each principle">
                <div class="space-y-1.5">
                    @foreach($principles as $principle)
                        @php($count = array_sum($principle['dist']))
                        <div class="grid grid-cols-[11rem_1fr_2.5rem] items-center gap-2 text-sm">
                            <span class="truncate text-gray-700" title="{{ $principle['name'] }}">{{ $principle['name'] }}</span>
                            <x-dashboard.score-bar :dist="$principle['dist']" height="h-3"/>
                            <span class="text-right text-xs tabular-nums text-gray-500">{{ $count ?: '–' }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="mt-3 text-xs text-gray-500">Needs a priority action ↔ principle mapping. A priority action linked to several principles counts under each, so rows don't add up.</p>
            </x-filament::section>
        </div>

        {{-- Document x PA --}}
        <x-filament::section class="header-dark dash-section" heading="DOCUMENTS × PRIORITY ACTIONS" description="Highest score found in each document for each priority action">
            <div class="overflow-x-auto">
                <table class="border-separate border-spacing-1 text-sm">
                    <thead>
                        <tr>
                            <th></th>
                            @foreach($heatmap['pas'] as $paId)
                                <th class="px-1 font-mono text-xs font-normal text-gray-500">{{ $paId }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($heatmap['rows'] as $row)
                            <tr>
                                <th class="pr-3 text-right text-sm font-normal whitespace-nowrap text-gray-700">{{ $row['name'] }}</th>
                                @foreach($row['values'] as $value)
                                    <td><x-dashboard.score-chip :score="$value" class="h-7 w-10"/></td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="mt-2 text-xs text-gray-500">Mockup shows a subset of priority actions. The full version would scroll or let you filter by recommendation.</p>
        </x-filament::section>
    </div>
</x-filament-panels::page>
