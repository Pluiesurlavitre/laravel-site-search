<?php

namespace Spatie\SiteSearch\SearchResults;

use Carbon\Carbon;
use Illuminate\Support\Traits\Macroable;

class Hit
{
    use Macroable;

    public function __construct(protected array $properties)
    {
    }

    public function __get(string $name): mixed
    {
        if ($name === 'dateModified') {
            return $this->dateModified();
        }

        return $this->properties[$name] ?? null;
    }

    public function dateModified(): ?Carbon
    {
        if (! $this->dateModifiedTimestamp) {
            return null;
        }

        return Carbon::createFromTimestamp($this->dateModifiedTimestamp);
    }

    public function title(): ?string
    {
        return $this->pageTitle ?? $this->h1;
    }

    public function snippet(): ?string
    {
        $propertyName = $this->getSnippetProperty();

        return $this->$propertyName;
    }

    public function highlightedSnippet(): ?string
    {
        $propertyName = $this->getSnippetProperty();

        $propertyName = ucfirst($propertyName);
        $propertyName = 'highlighted' . $propertyName;

        return $this->$propertyName;
    }

    protected function getSnippetProperty(): string
    {
        $propertyName = collect([
            'entry' => $this->entry,
            'description' => $this->description,
            'h1' => $this->h1,
        ])
            ->filter(fn (?string $value) => strlen($value) > 0)
            ->sortBy(fn (?string $value) => strlen($value))
            ->reverse()
            ->keys()
            ->first();

        return $propertyName ?? 'entry';
    }
}
