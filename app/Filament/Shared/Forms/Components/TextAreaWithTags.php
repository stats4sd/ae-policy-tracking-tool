<?php

namespace App\Filament\Shared\Forms\Components;

use Closure;
use Filament\Forms\Components\Textarea;

class TextAreaWithTags extends Textarea
{
    protected string $view = 'filament.shared.forms.components.text-area-with-tags';

    protected array|Closure|null $tags = [];

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
