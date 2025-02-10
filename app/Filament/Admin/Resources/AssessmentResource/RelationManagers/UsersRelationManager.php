<?php

namespace App\Filament\Admin\Resources\AssessmentResource\RelationManagers;

use App\Models\Assessment;
use App\Models\User;
use Awcodes\Shout\Components\Shout;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('invite users')
                    ->form([
                        Shout::make('info')
                            ->type('info')
                            ->content('Add the email address(es) of the user(s) you would like to invite with a role. An invitation will be sent to each address.')
                            ->columnSpanFull(),
                        Repeater::make('users')
                            ->label('Email Addresses to Invite')
                            ->simple(
                                TextInput::make('email')
                                    ->email()
                                    ->required(),
                            )
                            ->reorderable(false)
                            ->addActionLabel('Add Another Email Address'),
                    ])
                    ->action(fn (array $data, self $livewire) => $this->handleInvitation($data)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function handleInvitation(array $data): void
    {

        /** @var Assessment $assessment */
        $assessment = $this->getOwnerRecord();

        $assessment->sendInvites($data['users']);
    }
}
