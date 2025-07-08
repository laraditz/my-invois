<?php
namespace Laraditz\MyInvois\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Attributes
{
    public function __construct(
        public readonly array $attrs = []
    ) {
    }
}