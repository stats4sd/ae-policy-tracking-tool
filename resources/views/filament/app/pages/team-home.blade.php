@use('App\Filament\App\Pages\Summary')

<x-filament-panels::page>
    <div class="space-y-6">
        <x-dashboard.mock-banner>
            Team-only landing page for an assessment. All numbers are illustrative.
        </x-dashboard.mock-banner>

        <div class="flex flex-wrap items-baseline justify-between gap-2">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ $assessment['title'] }}</h2>
                <p class="text-sm text-gray-600">
                    {{ $assessment['jurisdiction'] }} ({{ $assessment['jurisdiction_type'] }})
                    · {{ $assessment['members'] }} team members
                    · last activity {{ $assessment['last_activity'] }}
                </p>
            </div>
            <x-filament::badge color="info" size="lg">In Progress</x-filament::badge>
        </div>

        <x-filament::section class="header-dark dash-section" heading="PROGRESS" description="What's left before this assessment can move to Review">
            <ol class="grid gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @foreach($progress['steps'] as $step)
                    <li class="flex flex-col items-center gap-2 text-center">
                        <span @class([
                            'flex h-10 w-10 items-center justify-center rounded-full text-sm font-semibold',
                            'bg-primary-600 text-white' => $step['state'] === 'done',
                            'bg-info-600 text-white' => $step['state'] === 'current',
                            'bg-gray-200 text-gray-600' => $step['state'] === 'todo',
                        ])>
                            @if($step['state'] === 'done')
                                <x-filament::icon icon="heroicon-m-check" class="h-5 w-5"/>
                            @else
                                {{ $loop->iteration }}
                            @endif
                        </span>
                        <span class="text-sm font-semibold text-gray-800">{{ $step['label'] }}</span>
                        <span class="text-xs text-gray-500">{{ $step['detail'] }}</span>
                    </li>
                @endforeach
            </ol>
        </x-filament::section>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($progress['todo'] as $item)
                <x-dashboard.stat :value="number_format($item['count'])" :label="$item['label']"/>
            @endforeach
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <x-filament::section class="header-dark dash-section" heading="EXTRACT REVIEW BY PRIORITY ACTION" description="Where most of the remaining work is">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                            <th class="py-2 font-medium">Priority action</th>
                            <th class="py-2 text-right font-medium">To review</th>
                            <th class="py-2 text-right font-medium">Not scored</th>
                            <th class="py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($progress['by_pa'] as $row)
                            <tr class="border-b border-gray-100 last:border-0">
                                <td class="py-2 font-mono">PA {{ $row['pa'] }}</td>
                                <td class="py-2 text-right tabular-nums">{{ $row['pending'] }}</td>
                                <td class="py-2 text-right tabular-nums">{{ $row['unscored'] }}</td>
                                <td class="py-2 text-right">
                                    <x-filament::link href="#" size="sm">Review</x-filament::link>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-filament::section>

            <div class="space-y-6">
                <x-filament::section class="header-dark dash-section" heading="STATEMENTS AND SUMMARY">
                    <div class="space-y-3 text-sm">
                        <p>
                            <span class="font-semibold">PAs with evidence but no statement:</span>
                            @foreach($progress['missing_statements'] as $pa)
                                <x-filament::badge color="gray" class="ml-1 inline-flex font-mono">{{ $pa }}</x-filament::badge>
                            @endforeach
                        </p>
                        <p class="text-gray-600">No key findings selected yet. Partners see the selected findings at the top of the summary once the assessment moves to Review.</p>
                        <div class="flex flex-wrap gap-2">
                            <x-filament::button tag="a" :href="Summary::getUrl()" size="sm" icon="heroicon-o-chart-bar">
                                View summary
                            </x-filament::button>
                            <x-filament::button tag="a" :href="Summary::getUrl(['prep' => 1])" size="sm" color="gray" icon="heroicon-o-star">
                                Select key findings
                            </x-filament::button>
                        </div>
                    </div>
                </x-filament::section>

                <x-filament::section class="header-dark dash-section" heading="RECENT ACTIVITY">
                    <ul class="space-y-2 text-sm">
                        @foreach($progress['activity'] as $entry)
                            <li class="flex gap-3">
                                <span class="w-14 shrink-0 font-mono text-xs text-gray-500">{{ $entry['when'] }}</span>
                                <span><span class="font-medium">{{ $entry['who'] }}</span> {{ $entry['what'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-3 text-xs text-gray-500">
                        {{ $assessment['members'] }} members · {{ $assessment['pending_invites'] }} pending invite.
                        Needs an activity log, which the app doesn't record yet.
                    </p>
                </x-filament::section>
            </div>
        </div>
    </div>
</x-filament-panels::page>
