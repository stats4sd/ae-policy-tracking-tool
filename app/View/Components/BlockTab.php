<?php

namespace App\View\Components;

use Illuminate\View\Component;

class BlockTab extends Component
{

    public function __construct(public bool $active, public string $title, public int $index)
    {
    }

    public function render()
    {
        return view('components.block-tab');
    }
}
