<?php
namespace Wysiwyg\ABTesting\Domain\Comparator;

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
class HexToIntDowncast
{
    /**
     * @param string $hex
     * @return float
     */
    public static function sixDigitHexToPercentageInteger(string $hex)
    {
        if (strlen($hex) !== 6) {
            throw new \RuntimeException(sprinf('Invalid provided hex length "%s".', strlen($hex)), 1625054451);
        }

        $randomInteger = base_convert($hex, 16, 10);

        // divide by maximum value FFFFFF = 16777216 decimal and then mult by 100, finally round

        return round($randomInteger / 16777216 * 100, 0);
    }
}
