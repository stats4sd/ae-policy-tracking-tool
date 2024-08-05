<?php

namespace App\Filament\Shared\Forms\Components;

use Closure;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;

class SimpleRepeaterWithTags extends Repeater
{
    protected string $view = 'filament.shared.forms.components.simple-repeater-with-tags';

    protected array | Closure | null $tags = [];


    public function tags(array|Closure|null $tags): static
    {
        $this->tags = $tags;

        return $this;
    }


    public function getTags(): array
    {
        return $this->evaluate($this->tags);
    }

}
