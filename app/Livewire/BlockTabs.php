<?php

namespace App\Livewire;

use Livewire\Component;

class BlockTabs extends Component
{
    public array $tabs;
    public int $activeTab = 1;

    public function render()
    {
        return view('components.block-tabs');
    }

    public function setActiveTab(int $index)
    {
        $this->activeTab = $index;
    }


}
