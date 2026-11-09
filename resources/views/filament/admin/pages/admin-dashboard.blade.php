@php
    $statusColor = fn (string $status) => match ($status) {
        'In Progress' => 'info',
        'Review' => 'warning',
        'Finalised' => 'success',
        default => 'gray',
    };
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        <x-dashboard.mock-banner>
            Platform overview of all assessments. All numbers are illustrative.
        </x-dashboard.mock-banner>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-dashboard.stat :value="$stats['assessments']" :label="'assessments in '.$stats['countries'].' countries'"/>
            <div class="border border-gray-200 bg-white p-4">
                <div class="flex flex-wrap gap-2">
                    @foreach($statuses as $statusName)
                        <x-filament::badge :color="$statusColor($statusName)">{{ $stats['statusCounts'][$statusName] ?? 0 }} {{ $statusName }}</x-filament::badge>
                    @endforeach
                </div>
                <div class="mt-2 text-sm text-gray-600">by status</div>
            </div>
            <x-dashboard.stat :value="$stats['documents']" label="policy documents"/>
            <x-dashboard.stat :value="$stats['verifiedPct'].'%'" label="of extracts verified (active assessments)" :percent="$stats['verifiedPct']"/>
        </div>

        <x-filament::section heading="Assessments" description="Progress and findings for every assessment. Select a row to open its summary.">
            <x-slot name="afterHeader">
                <x-filament::tabs>
                    <x-filament::tabs.item :active="$status === 'all'" wire:click="$set('status', 'all')">All</x-filament::tabs.item>
                    @foreach($statuses as $statusName)
                        <x-filament::tabs.item :active="$status === $statusName" wire:click="$set('status', '{{ $statusName }}')">{{ $statusName }}</x-filament::tabs.item>
                    @endforeach
                </x-filament::tabs>
            </x-slot>

            <div class="-mx-6 -my-6 overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs text-gray-500">
                            <th class="px-4 py-2 font-medium">Assessment</th>
                            <th class="px-4 py-2 font-medium">Status</th>
                            <th class="px-4 py-2 font-medium">Progress</th>
                            <th class="px-4 py-2 text-right font-medium">Docs</th>
                            <th class="px-4 py-2 text-right font-medium">PAs with evidence</th>
                            <th class="px-4 py-2 font-medium">Score mix</th>
                            <th class="px-4 py-2 text-right font-medium">Median policy year</th>
                            <th class="px-4 py-2 text-right font-medium">Key findings</th>
                            <th class="px-4 py-2 font-medium">Last activity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($portfolio as $row)
                            <tr class="cursor-pointer hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $row['title'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $row['country'] }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <x-filament::badge :color="$statusColor($row['status'])">{{ $row['status'] }}</x-filament::badge>
                                    @isset($row['in_review_days'])
                                        <div class="mt-1 text-xs text-gray-500">{{ $row['in_review_days'] }} days in review</div>
                                    @endisset
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex h-3 w-36 gap-0.5" title="{{ $progressSteps[min($row['progress'], count($progressSteps)) - 1] ?? '' }}">
                                        @foreach($progressSteps as $step)
                                            <span @class(['flex-1', 'bg-primary-600' => $loop->iteration <= $row['progress'], 'bg-gray-200' => $loop->iteration > $row['progress']]) title="{{ $step }}"></span>
                                        @endforeach
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">{{ $row['verified_pct'] }}% verified</div>
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ $row['docs'] }}</td>
                                <td class="px-4 py-3 text-right font-mono">{{ $row['pas'] }}/23</td>
                                <td class="px-4 py-3">
                                    <x-dashboard.score-bar :dist="$row['dist']" height="h-3" class="w-36"/>
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ $row['median_year'] }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ $row['findings'] ?: '–' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ $row['last_activity'] }}
                                    @if($row['days_idle'] > 60 && in_array($row['status'], ['In Progress', 'Review']))
                                        <x-filament::badge color="danger" size="sm" class="mt-1">stalled</x-filament::badge>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <div class="flex flex-wrap items-center justify-between gap-2">
            <p class="text-xs text-gray-500">Progress: {{ implode(' → ', $progressSteps) }}</p>
            <x-dashboard.score-legend :scores="$scores"/>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <x-filament::section heading="Needs attention">
                <ul class="space-y-3 text-sm">
                    @foreach($signals['attention'] as $signal)
                        <li class="flex items-start gap-2">
                            <x-filament::badge :color="$signal['color']" size="sm">{{ $signal['label'] }}</x-filament::badge>
                            <span class="text-gray-700">{{ $signal['detail'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-filament::section>

            <x-filament::section heading="Across finalised assessments">
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-gray-500">Most evidenced priority actions</dt>
                        <dd class="mt-1 flex gap-1">@foreach($signals['most_evidenced'] as $pa)<x-filament::badge color="success" class="font-mono">{{ $pa }}</x-filament::badge>@endforeach</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Least evidenced priority actions</dt>
                        <dd class="mt-1 flex gap-1">@foreach($signals['least_evidenced'] as $pa)<x-filament::badge color="gray" class="font-mono">{{ $pa }}</x-filament::badge>@endforeach</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Perverse incentives found most often</dt>
                        <dd class="mt-1 flex gap-1">@foreach($signals['perverse_hotspots'] as $pa)<x-filament::badge color="danger" class="font-mono">{{ $pa }}</x-filament::badge>@endforeach</dd>
                    </div>
                </dl>
            </x-filament::section>

            <x-filament::section heading="Search terms" description="Share of automatic matches kept after verification">
                <div class="space-y-2">
                    @foreach($signals['search_terms'] as $term)
                        <div class="grid grid-cols-[7rem_1fr_2.5rem] items-center gap-2 text-sm">
                            <span class="font-mono text-xs text-gray-700">{{ $term['term'] }}</span>
                            <div class="h-2 bg-gray-100"><div class="h-full bg-primary-600" style="width: {{ $term['kept'] }}%"></div></div>
                            <span class="text-right text-xs tabular-nums text-gray-500">{{ $term['kept'] }}%</span>
                        </div>
                    @endforeach
                </div>
                <p class="mt-3 text-xs text-gray-500">Helps tune the default search terms.</p>
            </x-filament::section>
        </div>

        <x-filament::section>
            This panel is visible to all admin users. Here, you can review and edit the core components of the assessment tool through the "Lookup Lists" pages, and manage user accounts and assessment teams through the "User Management" pages.
        </x-filament::section>
    </div>
</x-filament-panels::page>
