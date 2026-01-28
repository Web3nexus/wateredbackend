<?php

namespace App\Filament\Resources\Chapters\Pages;

use App\Filament\Resources\Chapters\ChapterResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Entry;

class CreateChapter extends CreateRecord
{
    protected static string $resource = ChapterResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Store verses_content temporarily, we'll process it after chapter is created
        $this->versesContent = $data['verses_content'] ?? null;
        unset($data['verses_content']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Process verses if provided
        if (!empty($this->versesContent)) {
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

    private $versesContent;
}
