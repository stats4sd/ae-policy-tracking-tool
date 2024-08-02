<?php

namespace App\Filament\Admin\Resources;

use App\Models\Assessment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AssessmentResource extends Resource
{
    protected static ?string $model = Assessment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('country_id')
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
                Tables\Columns\TextColumn::make('country.name')->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                                    ->sortable()
                                    ->date(),
                Tables\Columns\TextColumn::make('status')
                                    ->sortable()
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'In Progress' => 'warning',
                                        'Finalised' => 'success'
                                    }),
                Tables\Columns\TextColumn::make('finalised_at')
                                    ->sortable()
                                    ->date(),
            ])
            ->filters([
                SelectFilter::make('country')->relationship('country', 'name'),
                SelectFilter::make('status')
                        ->options([
                            'In Progress' => 'In Progress',
                            'Finalised' => 'Finalised',
                        ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('finalise')
                                ->icon(fn(Assessment $record): string => $record->finalised_at ? '' : 'heroicon-o-check')
                                ->label(fn(Assessment $record): string => $record->finalised_at ? '' : 'Mark as finalised')
                                ->color('success')
                                ->action(function (Assessment $record) {
                                    if($record->status==='In Progress') {
                                        $record->status = 'Finalised';
                                        $record->finalised_at = Carbon::now();
                                        $record->save();
                                    }
                                }),
                Tables\Actions\Action::make('viewReport')
                                ->label('View Report')
                                ->url('/report')
                                ->icon('heroicon-o-chart-bar-square')
                                ->openUrlInNewTab(),
            ])
            ->bulkActions([
                //
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Admin\Resources\AssessmentResource\RelationManagers\PoliciesRelationManager::class,
            \App\Filament\Admin\Resources\AssessmentResource\RelationManagers\AssessmentPriorityActionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\AssessmentResource\Pages\ListAssessments::route('/'),
            'create' => \App\Filament\Admin\Resources\AssessmentResource\Pages\CreateAssessment::route('/create'),
            'view' => \App\Filament\Admin\Resources\AssessmentResource\Pages\ViewAssessment::route('/{record}/view'),
        ];
    }
}
