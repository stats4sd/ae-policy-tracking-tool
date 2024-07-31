<?php

namespace App\View\Components;

use Illuminate\View\Component;

class BlockTabs extends Component
{

    public function __construct(
        public array $tabs,
        public int $activeTab = 1
    )
    {
    }

    public function render()
    {
        return view('components.block-tabs');
    }

}
