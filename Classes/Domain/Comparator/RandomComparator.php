<?php
namespace Wysiwyg\ABTesting\Domain\Comparator;

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
class RandomComparator implements ComparatorInterface
{
    /**
     * @var int
     */
    private $comparisonValue;


    public function getComparisonValue(): int
    {
        if ($this->comparisonValue !== null) {
            return $this->comparisonValue;
        }

        $randomBytes = random_bytes(3);
        $hexadecimalBytes = bin2hex($randomBytes);

        $this->comparisonValue = HexToIntDowncast::sixDigitHexToPercentageInteger($hexadecimalBytes);
        return $this->comparisonValue;
    }
}
