<?php
namespace Wysiwyg\ABTesting\Domain\Comparator;

interface ComparatorInterface
{
    public function getComparisonValue(): int;
}
