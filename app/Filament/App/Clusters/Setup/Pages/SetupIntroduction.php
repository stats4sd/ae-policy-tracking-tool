<?php

namespace App\Filament\App\Clusters\Setup\Pages;

use App\Filament\App\Clusters\Setup\SetupCluster;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class SetupIntroduction extends Page
{
    protected string $view = 'filament.app.clusters.setup.pages.setup-introduction';

    protected static ?string $cluster = SetupCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInformationCircle;

    protected static ?int $navigationSort = 0;

    public function assessmentInfoList(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Assessment Setup - Start Here!')
                ->components([
                    Text::make(new HtmlString("

                        <p class='mb-2'>Welcome to the Assessment Setup process. This guided setup will help you configure your assessment step-by-step.</p>
                        <p class='mb-2'>Before starting the assessment, please complete the information in this '0. Setup' section. Use the links in the sidebar to complete the sections:</p>
                        <ul class='list-disc list-inside mb-2'>
                        <li><b>Assessment Details:</b> Review the key metadata about the assessment.</li>
                        <li><b>Team Members:</b> View, invite, and remove team members who can collaborate on this assessment.</li>
                        <li><b>Search Terms:</b> Review the search terms that will be used for automatic searching of the documents you upload during the assessment.</li>
                        </ul>

                    ")),
                ]),
        ]);
    }
}
