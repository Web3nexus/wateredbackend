<?php

namespace App\Filament\Resources\Chapters\Pages;

use App\Filament\Resources\Chapters\ChapterResource;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\EditRecord;
use App\Models\Entry;

class EditChapter extends EditRecord
{
    protected static string $resource = ChapterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('bulkAddVerses')
                ->label('Bulk Add Verses')
                ->icon('heroicon-o-document-plus')
                ->modalHeading('Bulk Add Verses')
                ->modalDescription('Import multiple verses at once. You can choose to either append these to existing verses or replace everything.')
                ->form([
                    Textarea::make('verses_content')
                        ->label('Verses Content')
                        ->rows(15)
                        ->required()
                        ->helperText('Format: "1. Verse text" - Each line starting with a number becomes a verse.'),
                    Toggle::make('overwrite')
                        ->label('Overwrite existing verses?')
                        ->default(false)
                        ->helperText('If enabled, all existing verses for this chapter will be deleted first.'),
                ])
                ->action(function (array $data) {
                    if ($data['overwrite']) {
                        $this->record->entries()->delete();
                    }

                    $this->createVersesFromContent($data['verses_content']);

                    $this->notify('success', 'Verses processed successfully.');

                    return redirect(request()->header('Referer'));
                }),
            Actions\DeleteAction::make(),
        ];
    }

    private function createVersesFromContent(string $content): void
    {
        $lines = explode("\n", $content);
        $verseNumber = 1;

        // If not overwriting, start from the last verse number + 1
        if ($this->record->entries()->exists()) {
            $verseNumber = $this->record->entries()->max('number') + 1;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line))
                continue;

            // Match lines starting with number followed by period or colon
            if (preg_match('/^(\d+)[\.\:\)]\s*(.+)$/', $line, $matches)) {
                $number = (int) $matches[1];
                $text = trim($matches[2]);

                Entry::create([
                    'chapter_id' => $this->record->id,
                    'number' => $number,
                    'text' => $text,
                    'order' => $number,
                    'is_active' => true,
                ]);

                $verseNumber = max($verseNumber, $number + 1);
            } else {
                // If line doesn't start with number, treat as continuation
                Entry::create([
                    'chapter_id' => $this->record->id,
                    'number' => $verseNumber,
                    'text' => $line,
                    'order' => $verseNumber,
                    'is_active' => true,
                ]);

                $verseNumber++;
            }
        }
    }
}
