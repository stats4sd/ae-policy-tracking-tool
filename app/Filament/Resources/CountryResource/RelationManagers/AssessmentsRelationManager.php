<?php

namespace App\Filament\Resources\CountryResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Assessment;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AssessmentResource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class AssessmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assessments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // 
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('created_at')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                                    ->sortable()
                                    ->dateTime(),
                Tables\Columns\TextColumn::make('status')
                                    ->sortable()
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'In Progress' => 'warning',
                                        'Finalised' => 'success'
                                    }),
                Tables\Columns\TextColumn::make('finalised_at')
                                    ->sortable()
                                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            -> recordUrl(fn(Assessment $record) => AssessmentResource::getUrl('edit', ['record' => $record])
            );
    }
}
