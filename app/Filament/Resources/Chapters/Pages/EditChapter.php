<?php

namespace App\Filament\Resources\Chapters\Pages;

use App\Filament\Resources\Chapters\ChapterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Entry;

class EditChapter extends EditRecord
{
    protected static string $resource = ChapterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load existing verses into the textarea
        $verses = $this->record->entries()->orderBy('number')->get();

        if ($verses->isNotEmpty()) {
            $versesContent = $verses->map(function ($entry) {
                return $entry->number . '. ' . $entry->text;
            })->implode("\n");

            $data['verses_content'] = $versesContent;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store verses_content temporarily
        $this->versesContent = $data['verses_content'] ?? null;
        unset($data['verses_content']);

        return $data;
    }

    protected function afterSave(): void
    {
        // Update verses if provided
        if (!empty($this->versesContent)) {
            // Delete existing verses
            $this->record->entries()->delete();

            // Create new verses from content
            $this->createVersesFromContent($this->versesContent);
        }
    }

    private function createVersesFromContent(string $content): void
    {
        $lines = explode("\n", $content);
        $verseNumber = 1;

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

                $verseNumber++;
            } else {
                // If line doesn't start with number, treat as continuation or new verse
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

    private $versesContent;
}
