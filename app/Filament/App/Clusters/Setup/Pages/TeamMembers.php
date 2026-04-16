<?php

namespace App\Filament\App\Clusters\Setup\Pages;

use App\Filament\App\Clusters\Setup\SetupCluster;
use App\Filament\App\Widgets\PendingInvitesWidget;
use App\Models\Assessment;
use App\Models\User;
use Awcodes\Shout\Components\Shout;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class TeamMembers extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $cluster = SetupCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Team Members';

    protected string $view = 'filament.app.clusters.setup.pages.team-members';

    protected function getFooterWidgets(): array
    {
        return [
            PendingInvitesWidget::class,
        ];
    }

    public function getAssessment(): Assessment
    {
        /** @var Assessment $assessment */
        $assessment = Filament::getTenant();

        return $assessment;
    }

    public function table(Table $table): Table
    {
        $assessment = $this->getAssessment();

        return $table
            ->query($assessment->users()->getQuery())
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
            ])
            ->headerActions([
                Action::make('inviteMembers')
                    ->label('Invite Members')
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->schema([
                        Shout::make('info')
                            ->type('info')
                            ->content('Enter the email address(es) of people you would like to invite. An invitation email will be sent to each address.')
                            ->columnSpanFull(),
                        Repeater::make('emails')
                            ->label('Email Addresses to Invite')
                            ->simple(
                                TextInput::make('email')
                                    ->email()
                                    ->required(),
                            )
                            ->reorderable(false)
                            ->addActionLabel('Add Another Email Address'),
                    ])
                    ->action(function (array $data) use ($assessment): void {
                        $assessment->sendInvites($data['emails']);
                    }),
            ])
            ->recordActions([
                Action::make('removeMember')
                    ->label('Remove')
                    ->icon(Heroicon::OutlinedUserMinus)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->hidden(fn (User $record): bool => $record->id === auth()->id())
                    ->action(function (User $record) use ($assessment): void {
                        $assessment->users()->detach($record->id);

                        Notification::make()
                            ->success()
                            ->title('Member removed.')
                            ->send();
                    }),
            ]);
    }
}
