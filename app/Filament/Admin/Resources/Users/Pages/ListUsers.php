<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use Awcodes\Shout\Components\Shout;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;

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

    public function getInfoPanel(): Section
    {
        return Section::make('Information')
            ->icon(Heroicon::InformationCircle)
            ->schema([
                Text::make('Manage users and their roles. You can invite new users or create them directly, and assign them to assessments.'),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getTabsContentComponent(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),
                $this->getInfoPanel(),
                EmbeddedTable::make(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER),
            ]);
    }
}
