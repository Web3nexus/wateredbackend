<?php

namespace App\Filament\Resources\DailyWisdoms\Pages;

use App\Filament\Resources\DailyWisdoms\DailyWisdomResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDailyWisdoms extends ListRecords
{
    protected static string $resource = DailyWisdomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('quickPublish')
                ->label('Quick Publish')
                ->icon('heroicon-o-rocket-launch')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Quick Publish Daily Wisdom')
                ->modalDescription('This will set the selected wisdom as active for today.')
                ->form([
                    \Filament\Forms\Components\Select::make('wisdom_id')
                        ->label('Select Wisdom')
                        ->options(
                            \App\Models\DailyWisdom::where('is_active', false)
                                ->orWhereDate('active_date', '>', now())
                                ->pluck('quote', 'id')
                                ->map(fn($quote) => \Illuminate\Support\Str::limit($quote, 60))
                        )
                        ->required()
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    $wisdom = \App\Models\DailyWisdom::find($data['wisdom_id']);
                    if ($wisdom) {
                        $wisdom->update([
                            'active_date' => now()->toDateString(),
                            'is_active' => true,
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Wisdom Published!')
                            ->body('Daily wisdom has been set for today.')
                            ->send();
                    }
                }),
        ];
    }
}
