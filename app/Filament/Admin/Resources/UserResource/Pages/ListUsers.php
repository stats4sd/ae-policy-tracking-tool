<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Awcodes\Shout\Components\Shout;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('invite users')
                ->schema([
                    Shout::make('info')
                        ->type('info')
                        ->content('Add the email address(es) of the user(s) you would like to invite with a role. An invitation will be sent to each address.')
                        ->columnSpanFull(),
                    Repeater::make('users')
                        ->label('Email Addresses to Invite')
                        ->schema([
                            TextInput::make('email')
                                ->email()
                                ->required(),

                            Select::make('role')
                                ->relationship('roles', 'name')
                                ->required(),
                        ])
                        ->reorderable(false)
                        ->addActionLabel('Add Another Email Address'),
                ])
                ->action(fn (array $data, ListRecords $livewire) => $this->handleInvitation($data)),
            CreateAction::make(),
        ];
    }

    public function handleInvitation(array $data): void
    {

        $user = auth()->user();
        $user->sendInvites($data['users']);
    }
}
