<?php
namespace Laraditz\MyInvois\Attributes;

use Attribute;

#[Attribute]
class Attributes
{
    public function __construct(
        public readonly array $attrs = []
    ) {
    }
}