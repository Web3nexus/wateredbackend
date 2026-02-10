<?php

namespace App\Filament\Resources\Newsletters;

use App\Filament\Resources\Newsletters\Pages\CreateNewsletter;
use App\Filament\Resources\Newsletters\Pages\EditNewsletter;
use App\Filament\Resources\Newsletters\Pages\ListNewsletters;
use App\Models\Newsletter;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class NewsletterResource extends Resource
{
    protected static ?string $model = Newsletter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('subject')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                RichEditor::make('content')
                    ->required()
                    ->columnSpanFull(),
                Select::make('recipients_type')
                    ->options([
                        'all' => 'All Users',
                        'subscribers' => 'Subscribers Only',
                    ])
                    ->default('all')
                    ->required(),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                    ])
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('sent_at')
                    ->disabled()
                    ->label('Sent At'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('recipients_type'),
                TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                Action::make('send')
                    ->label('Send Now')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    ->action(function (Newsletter $record) {
                        \App\Jobs\SendNewsletterJob::dispatch($record);

                        \Filament\Notifications\Notification::make()
                            ->title('Newsletter Sending Initiated')
                            ->body('The newsletter is being sent to recipients in the background.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn(Newsletter $record) => $record->status !== 'sent'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNewsletters::route('/'),
            'create' => CreateNewsletter::route('/create'),
            'edit' => EditNewsletter::route('/{record}/edit'),
        ];
    }
}
