<?php

namespace App\Livewire;

use Livewire\Component;

class BlockTabs extends Component
{
    public array $tabs;
    public int $activeTab = 0;

    public function render()
    {
        return view('components.block-tabs');
    }

    public function setActiveTab(int $id)
    {
        $this->activeTab = $id;
        $this->dispatch('tabChanged', $id);
    }


}
