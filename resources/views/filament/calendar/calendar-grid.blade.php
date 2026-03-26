<x-filament-panels::page>
    <div class="space-y-4">

        {{-- Month Navigator --}}
        <div class="flex items-center justify-between px-2">
            <button wire:click="prevMonth"
                class="flex items-center gap-1 px-4 py-2 text-sm font-bold rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                ← Prev
            </button>

            <div class="text-center">
                <p class="text-xl font-bold font-['Cinzel'] tracking-widest uppercase">
                    {{ $this->getMonthName() }}
                </p>
            </div>

            <div class="flex gap-2">
                <button wire:click="goToToday"
                    class="px-4 py-2 text-sm font-bold rounded-xl bg-primary-500/20 hover:bg-primary-500/30 border border-primary-500/40 text-primary-400 transition">
                    Today
                </button>
                <button wire:click="nextMonth"
                    class="flex items-center gap-1 px-4 py-2 text-sm font-bold rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                    Next →
                </button>
            </div>
        </div>

        {{-- Day-of-week header --}}
        <div class="grid grid-cols-7 gap-1 px-1">
            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $wd)
                <div class="text-center text-xs font-bold tracking-widest text-gray-500 py-2 uppercase">
                    {{ $wd }}
                </div>
            @endforeach
        </div>

        {{-- Calendar Grid --}}
        <div class="grid grid-cols-1 gap-1 px-1">
            @foreach($this->getCalendarGrid() as $week)
                <div class="grid grid-cols-7 gap-1">
                    @foreach($week as $cell)
                        @if($cell === null)
                            <div class="rounded-xl h-24 bg-white/[0.01] border border-white/5"></div>
                        @else
                            @php
                                $record  = $cell['record'];
                                $isToday = $cell['isToday'];
                                $isSacred = $record && $record->is_sacred;
                                $hasCelebration = $record && !empty($record->celebration_type);
                                $editUrl = $record
                                    ? \App\Filament\Resources\CalendarDays\CalendarDayResource::getUrl('edit', ['record' => $record->id])
                                    : null;
                            @endphp

                            <a href="{{ $editUrl ?? '#' }}"
                                @if(!$editUrl) title="No calendar entry for this day yet — add one via the calendar admin." @endif
                                class="group relative block rounded-xl h-24 border transition-all duration-200 cursor-pointer no-underline
                                    {{ $isToday
                                        ? 'border-primary-500 bg-primary-500/15 shadow-lg shadow-primary-500/10'
                                        : ($isSacred
                                            ? 'border-amber-500/30 bg-amber-500/5 hover:bg-amber-500/10'
                                            : 'border-white/10 bg-white/[0.02] hover:bg-white/[0.06]') }}">

                                <div class="flex flex-col justify-between h-full p-2">

                                    {{-- Day number --}}
                                    <div class="flex items-start justify-between">
                                        <span class="text-sm font-bold
                                            {{ $isToday ? 'text-primary-400' : 'text-gray-300' }}">
                                            {{ $cell['day'] }}
                                        </span>

                                        @if($isSacred)
                                            <span class="text-amber-400 text-xs leading-none">✦</span>
                                        @endif
                                    </div>

                                    {{-- Bottom info --}}
                                    <div class="space-y-0.5">
                                        @if($hasCelebration)
                                            <div class="flex items-center gap-1">
                                                <span class="text-xs">🎉</span>
                                                <span class="text-[10px] font-bold text-amber-400 truncate leading-tight">
                                                    {{ $record->celebration_type }}
                                                </span>
                                            </div>
                                        @endif

                                        @if($record && $record->custom_day_name)
                                            <p class="text-[10px] text-gray-500 truncate leading-tight">
                                                {{ $record->custom_day_name }}
                                            </p>
                                        @endif

                                        @if(!$record)
                                            <p class="text-[9px] text-gray-700 leading-tight">No data</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Hover overlay hint --}}
                                @if($record)
                                    <div class="absolute inset-0 rounded-xl flex items-center justify-center
                                        opacity-0 group-hover:opacity-100 transition-opacity duration-200
                                        bg-black/40 backdrop-blur-[2px]">
                                        <span class="text-xs text-white font-bold tracking-wide">
                                            ✏️ Edit Day
                                        </span>
                                    </div>
                                @endif
                            </a>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap gap-4 px-2 pt-2 text-xs text-gray-500">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-sm bg-primary-500/20 border border-primary-500"></div>
                <span>Today</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-sm bg-amber-500/10 border border-amber-500/30"></div>
                <span>Sacred Day</span>
            </div>
            <div class="flex items-center gap-2">
                <span>🎉</span>
                <span>Has Celebration</span>
            </div>
            <div class="flex items-center gap-2">
                <span>✦</span>
                <span>Sacred marker</span>
            </div>
        </div>
    </div>
</x-filament-panels::page>
