<?php

namespace App\Filament\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\AssessmentResource\Pages\ListAssessments;
use App\Filament\Admin\Resources\AssessmentResource\Pages\CreateAssessment;
use App\Filament\Admin\Resources\AssessmentResource\Pages\ViewAssessment;
use App\Filament\Admin\Resources\AssessmentResource\RelationManagers\UsersRelationManager;
use App\Models\Assessment;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Stats4sd\FilamentTeamManagement\Filament\Admin\Resources\Teams\RelationManagers\InvitesRelationManager;

class AssessmentResource extends Resource
{
    protected static ?string $model = Assessment::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('country_id')
                                    ->placeholder('Select a country')
                                    ->relationship('country', 'name')
                                    ->hiddenOn(['view'])
                                    ->required()
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('country.name')->sortable(),
                TextColumn::make('created_at')
                                    ->sortable()
                                    ->date(),
                TextColumn::make('status')
                                    ->sortable()
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'In Progress' => 'warning',
                                        'Finalised' => 'success',
                                        'Review' => 'info',
                                        default => 'primary',
                                    }),
                TextColumn::make('finalised_at')
                                    ->sortable()
                                    ->date(),
            ])
            ->filters([
                SelectFilter::make('country')->relationship('country', 'name'),
                SelectFilter::make('status')
                        ->options([
                            'In Progress' => 'In Progress',
                            'Review' => 'Review',
                            'Finalised' => 'Finalised',
                        ])
            ])
            ->recordActions([
                ViewAction::make(),
//                Tables\Actions\Action::make('finalise')
//                                ->icon(fn(Assessment $record): string => $record->finalised_at ? '' : 'heroicon-o-check')
//                                ->label(fn(Assessment $record): string => $record->finalised_at ? '' : 'Mark as finalised')
//                                ->color('success')
//                                ->action(function (Assessment $record) {
//                                    if($record->status==='In Progress') {
//                                        $record->status = 'Finalised';
//                                        $record->finalised_at = Carbon::now();
//                                        $record->save();
//                                    }
//                                }),
//                Tables\Actions\Action::make('viewReport')
//                                ->label('View Report')
//                                ->url('/report')
//                                ->icon('heroicon-o-chart-bar-square')
//                                ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                //
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UsersRelationManager::class,
            InvitesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssessments::route('/'),
            'create' => CreateAssessment::route('/create'),
            'view' => ViewAssessment::route('/{record}/view'),
        ];
    }
}
