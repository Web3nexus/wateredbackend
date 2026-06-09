<?php

namespace App\Filament\Resources\AdminUsers\Pages;

use App\Filament\Resources\AdminUsers\AdminUserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditAdminUser extends EditRecord
{
    protected static string $resource = AdminUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn() => $this->record?->isDeveloper() && !auth('admin')->user()->isDeveloper()),
        ];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->record->isDeveloper() && !auth('admin')->user()->isDeveloper()) {
            Notification::make()
                ->title('Access Denied')
                ->body('Developer accounts can only be edited by another Developer.')
                ->danger()
                ->send();

            $this->redirect(AdminUserResource::getUrl('index'));
        }
    }
}
