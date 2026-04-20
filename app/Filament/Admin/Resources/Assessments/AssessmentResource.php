<?php

namespace App\Filament\Admin\Resources\Assessments;

use App\Filament\Admin\Resources\Assessments\Pages\ListAssessments;
use App\Filament\Admin\Resources\Assessments\Pages\ViewAssessment;
use App\Filament\Admin\Resources\Assessments\RelationManagers\UsersRelationManager;
use App\Models\Assessment;
use App\Models\Country;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get as Get;
use Filament\Schemas\Components\Utilities\Set as Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Stats4sd\FilamentTeamManagement\Filament\Admin\Resources\Teams\RelationManagers\InvitesRelationManager;

class AssessmentResource extends Resource
{
    protected static ?string $model = Assessment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('country_id')
                    ->label('Country')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')->required(),
                    ])
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get, ?int $state): void {
                        $country = $state ? Country::find($state) : null;
                        $year = $get('year');
                        $set('title', implode(' - ', array_filter([$country?->name, $year])));
                    })
                    ->required(),
                Select::make('year')
                    ->label('Year of Assessment')
                    ->options(
                        collect(range(now()->year + 1, 2000))
                            ->mapWithKeys(fn (int $y) => [$y => $y])
                    )
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                        $countryId = $get('country_id');
                        $country = $countryId ? Country::find($countryId) : null;
                        $set('title', implode(' - ', array_filter([$country?->name, $state])));
                    }),
                TextInput::make('title')
                    ->label('Assessment Title')
                    ->helperText('Auto-generated from country and year, but can be customised.')
                    ->columnSpanFull(),
                Select::make('language_id')
                    ->label('Primary Language')
                    ->relationship('language', 'name')
                    ->preload(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('country.name')->sortable(),
                TextColumn::make('created_at')
                    ->sortable()
                    ->date(),
                SelectColumn::make('status')
                    ->options([
                        'In Progress' => 'In Progress',
                        'Review' => 'Review',
                        'Finalised' => 'Finalised',
                    ]),
                TextColumn::make('finalised_at')
                    ->sortable()
                    ->date(),
                TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Team Members')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('policy_documents_count')
                    ->counts('policyDocuments')
                    ->label('Policy Docs')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('verified_extracts_count')
                    ->counts('verifiedExtracts')
                    ->label('Verified Extracts')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('statements_count')
                    ->counts('statements')
                    ->label('Statements')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('country')->relationship('country', 'name'),
                SelectFilter::make('status')
                    ->options([
                        'In Progress' => 'In Progress',
                        'Review' => 'Review',
                        'Finalised' => 'Finalised',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (Assessment $record): bool =>
                        ($record->verified_extracts_count ?? $record->verifiedExtracts()->count()) === 0
                        && ($record->statements_count ?? $record->statements()->count()) === 0
                    ),
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
            'view' => ViewAssessment::route('/{record}/view'),
        ];
    }
}
