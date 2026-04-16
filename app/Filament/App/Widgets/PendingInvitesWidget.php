<?php

namespace App\Filament\App\Widgets;

use App\Models\Invite;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class PendingInvitesWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $assessment = Filament::getTenant();

        return $table
            ->heading('Pending Invitations')
            ->query($assessment->invites()->getQuery())
            ->columns([
                TextColumn::make('email'),
                TextColumn::make('created_at')
                    ->label('Invited At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('cancelInvite')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Invite $record) => $record->delete()),
            ]);
    }
}
