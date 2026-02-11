<?php

namespace App\Livewire;

use Illuminate\View\Component;

class BlockTab extends Component
{
    public bool $active;

    public string $title;

    public int $index;

    public function render()
    {
        return view('components.block-tab');
    }
}
