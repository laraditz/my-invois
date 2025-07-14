<?php

namespace Laraditz\MyInvois\Traits;

trait HasAttributes
{
    public array $attributes = [];

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function attributes(array $attributes): static
    {
        $this->setAttributes($attributes);

        return $this;
    }
}