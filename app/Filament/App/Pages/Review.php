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
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.app.pages.review';

    protected ?string $heading = 'PREVIEW REPORT';


    public ?Assessment $assessment;
    public ?Collection $recommendations;

    public function __construct()
    {
        $this->assessment = HelperService::getCurrentTenant();

        $this->recommendations = Recommendation::all()
            ->load([
                'priorityActions.assessmentPriorityActions.statements.policies',
                'aePrinciples',
            ]);

        ray($this->recommendations->first()->priorityActions->first()->assessmentPriorityActions->first());

    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->url(route('assessment.print-review', ['assessment' => $this->assessment->id])),
        ];
    }

}
