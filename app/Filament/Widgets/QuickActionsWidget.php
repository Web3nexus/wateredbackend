<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Audios\AudioResource;
use App\Filament\Resources\Entries\EntryResource;
use App\Filament\Resources\TextCollections\TextCollectionResource;
use App\Filament\Resources\Rituals\RitualResource;
use App\Filament\Resources\Incantations\IncantationResource;
use App\Filament\Resources\Events\EventResource;
use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    protected string $view = 'filament.widgets.quick-actions-widget';

    protected int|string|array $columnSpan = 'full';

    public function getActions(): array
    {
        return [
            [
                'label' => 'Add New Verse',
                'icon' => 'heroicon-o-chat-bubble-bottom-center-text',
                'description' => 'Add a specific verse or prose to a chapter.',
                'url' => EntryResource::getUrl('create'),
                'color' => 'primary',
            ],

            [
                'label' => 'Add Audio Teaching',
                'icon' => 'heroicon-o-microphone',
                'description' => 'Add a recitation or spoken teaching.',
                'url' => AudioResource::getUrl('create'),
                'color' => 'warning',
            ],
            [
                'label' => 'Add New Book',
                'icon' => 'heroicon-o-book-open',
                'description' => 'Create a new sacred text or scripture collection.',
                'url' => TextCollectionResource::getUrl('create'),
                'color' => 'danger',
            ],
            [
                'label' => 'Add New Ritual',
                'icon' => 'heroicon-o-sparkles',
                'description' => 'Add a new spiritual ritual or practice.',
                'url' => RitualResource::getUrl('create'),
                'color' => 'success',
            ],
            [
                'label' => 'Add Incantation',
                'icon' => 'heroicon-o-musical-note',
                'description' => 'Add a new sacred chant or incantation.',
                'url' => IncantationResource::getUrl('create'),
                'color' => 'primary',
            ],
            [
                'label' => 'Add New Event',
                'icon' => 'heroicon-o-calendar',
                'description' => 'Schedule a new community gathering or ceremony.',
                'url' => EventResource::getUrl('create'),
                'color' => 'info',
            ],
        ];
    }
}
