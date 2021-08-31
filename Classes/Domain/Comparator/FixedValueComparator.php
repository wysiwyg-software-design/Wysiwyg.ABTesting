<?php
namespace Wysiwyg\ABTesting\Domain\Comparator;

use Neos\Flow\Annotations as Flow;


/**
 * @Flow\Proxy(false)
 */
class FixedValueComparator implements ComparatorInterface
{
    private $comparisonValue;

    public function __construct(int $comparisonValue)
    {
        $this->comparisonValue = $comparisonValue;
    }

    public function getComparisonValue(): int
    {
        return $this->comparisonValue;
    }
}
