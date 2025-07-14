<?php

namespace Laraditz\MyInvois\Contracts;

interface WithAttributes
{
    public function getAttributes(): array;

    public function setAttributes(array $attributes): void;

    public function attributes(array $attributes): static;
}