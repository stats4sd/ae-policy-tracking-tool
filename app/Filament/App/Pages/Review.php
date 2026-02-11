<?php

namespace App\Filament\App\Pages;

use App\Models\Assessment;
use App\Models\Recommendation;
use App\Services\HelperService;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;

class Review extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.app.pages.review';

    protected ?string $heading = 'PREVIEW REPORT';

    public ?Assessment $assessment;

    public ?Collection $recommendations;

    public function __construct()
    {
        $this->assessment = HelperService::getCurrentTenant();

        $this->recommendations = Recommendation::all()
            ->load([
                'priorityActions.statements.policyDocuments',
                'aePrinciples',
            ]);

    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->modalHeading('Not Yet Available')
                ->modalDescription('This feature is not yet available.')
                ->modalSubmitAction(false),
        ];
    }
}
