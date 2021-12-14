<?php

namespace Wysiwyg\ABTesting\Domain\Decider;

class PercentageAbcDecider extends PercentageDecider
{
    protected $possibleDecisions = ['a' => 'a', 'b' => 'b', 'c' => 'c'];

    public function getTitle()
    {
        return 'PercentageAbcDecider';
    }

    public function __toString()
    {
        return PercentageAbcDecider::class;
    }
}
