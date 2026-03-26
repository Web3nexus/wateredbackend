<?php

namespace App\Filament\Resources\CalendarDays\Pages;

use App\Filament\Resources\CalendarDays\CalendarDayResource;
use App\Models\CalendarDay;
use App\Models\CalendarMonth;
use Carbon\Carbon;
use Filament\Resources\Pages\Page;

class ListCalendarDays extends Page
{
    protected static string $resource = CalendarDayResource::class;

    protected string $view = 'filament.calendar.calendar-grid';

    public int $viewYear;
    public int $viewMonth;

    public function mount(): void
    {
        $this->viewYear  = now()->year;
        $this->viewMonth = now()->month;
    }

    public function prevMonth(): void
    {
        $dt = Carbon::createFromDate($this->viewYear, $this->viewMonth, 1)->subMonth();
        $this->viewYear  = $dt->year;
        $this->viewMonth = $dt->month;
    }

    public function nextMonth(): void
    {
        $dt = Carbon::createFromDate($this->viewYear, $this->viewMonth, 1)->addMonth();
        $this->viewYear  = $dt->year;
        $this->viewMonth = $dt->month;
    }

    public function goToToday(): void
    {
        $this->viewYear  = now()->year;
        $this->viewMonth = now()->month;
    }

    /**
     * Build a 6-week grid for the current view month.
     * Each slot is either null (empty) or a CalendarDay record (may still be null if not in DB).
     */
    public function getCalendarGrid(): array
    {
        $firstDay    = Carbon::createFromDate($this->viewYear, $this->viewMonth, 1);
        $daysInMonth = $firstDay->daysInMonth;

        // Sunday=0 offset
        $startOffset = $firstDay->dayOfWeek; // 0=Sun

        // Pre-load all calendar_days for this month
        $month = CalendarMonth::where('number', $this->viewMonth)->first();
        $dayMap = [];
        if ($month) {
            CalendarDay::where('calendar_month_id', $month->id)->get()->each(function ($d) use (&$dayMap) {
                $dayMap[$d->day_number] = $d;
            });
        }

        $cells = [];

        // Leading empty cells
        for ($i = 0; $i < $startOffset; $i++) {
            $cells[] = null;
        }

        // Day cells
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $cells[] = [
                'day'    => $d,
                'year'   => $this->viewYear,
                'month'  => $this->viewMonth,
                'record' => $dayMap[$d] ?? null,
                'isToday' => now()->year === $this->viewYear
                            && now()->month === $this->viewMonth
                            && now()->day === $d,
            ];
        }

        // Pad trailing to fill 6 rows
        while (count($cells) % 7 !== 0) {
            $cells[] = null;
        }

        return array_chunk($cells, 7);
    }

    public function getMonthName(): string
    {
        return Carbon::createFromDate($this->viewYear, $this->viewMonth, 1)->format('F Y');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
