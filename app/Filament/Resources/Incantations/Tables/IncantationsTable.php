<?php

namespace App\Filament\Resources\Incantations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class IncantationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('audio_url')
                    ->searchable(),
                ToggleColumn::make('is_premium')
                    ->label('Is Premium?'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('category')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn($record) => route('admin.export.incantation', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    \Filament\Actions\BulkAction::make('export_pdf_bulk')
                        ->label('Export Selection (PDF)')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $data = [
                                'items' => $records,
                                'type' => 'Incantation',
                                'date' => now()->format('F d, Y')
                            ];
                            ini_set('memory_limit', '512M');
                            set_time_limit(300);
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.spiritual-practices-bulk', $data);
                            $pdf->setPaper('a4', 'portrait');
                            return response()->streamDownload(fn () => print($pdf->output()), 'incantations-export-' . now()->format('Y-m-d') . '.pdf');
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
