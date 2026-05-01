<?php

namespace App\Filament\Admin\Resources\Assessments;

use App\Enums\AssessmentStatus;
use App\Enums\JurisdictionType;
use App\Filament\Admin\Resources\Assessments\Pages\ListAssessments;
use App\Filament\Admin\Resources\Assessments\Pages\ViewAssessment;
use App\Filament\Admin\Resources\Assessments\RelationManagers\UsersRelationManager;
use App\Filament\App\Pages\AssessmentOverview;
use App\Models\Assessment;
use App\Models\Jurisdiction;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
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
                Select::make('jurisdiction_id')
                    ->label('Jurisdiction')
                    ->relationship('jurisdiction', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')->label('Jurisdiction Name')->required(),
                        Select::make('type')
                            ->label('Type')
                            ->options(JurisdictionType::class)
                            ->required()
                            ->live(),
                        Select::make('country_id')
                            ->label('Country')
                            ->relationship('country', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get) => in_array(
                                $get('type'),
                                [JurisdictionType::National->value, JurisdictionType::Subnational->value]
                            )),
                    ])
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get, ?int $state): void {
                        $jurisdiction = $state ? Jurisdiction::find($state) : null;
                        $year = $get('year');
                        $set('title', implode(' - ', array_filter([$jurisdiction?->name, $year])));
                    }),
                Select::make('year')
                    ->label('Year of Assessment')
                    ->options(
                        collect(range(now()->year + 1, 2000))
                            ->mapWithKeys(fn (int $y) => [$y => $y])
                    )
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                        $jurisdictionId = $get('jurisdiction_id');
                        $jurisdiction = $jurisdictionId ? Jurisdiction::find($jurisdictionId) : null;
                        $set('title', implode(' - ', array_filter([$jurisdiction?->name, $state])));
                    }),
                TextInput::make('title')
                    ->label('Assessment Title')
                    ->helperText('Auto-generated from jurisdiction and year, but can be customised.')
                    ->columnSpanFull(),
                Select::make('language_id')
                    ->label('Primary Language')
                    ->required()
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
                TextColumn::make('jurisdiction.name')->sortable(),
                TextColumn::make('created_at')
                    ->sortable()
                    ->date(),
                SelectColumn::make('status')
                    ->options(AssessmentStatus::class),
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
                SelectFilter::make('jurisdiction')->relationship('jurisdiction', 'name'),
                SelectFilter::make('status')
                    ->options(AssessmentStatus::class),
                TrashedFilter::make(),

            ])
            ->recordActions([
                Action::make('view_assessment')
                    ->icon(Heroicon::Eye)
                    ->label('View')
                    ->url(fn (Assessment $record) => AssessmentOverview::getUrl(panel: 'app', tenant: $record)),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
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
