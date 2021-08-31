<?php

namespace Wysiwyg\ABTesting\Domain\Decider;

use Wysiwyg\ABTesting\Domain\Comparator\ComparatorInterface;

interface DeciderInterface
{
    /**
     * @param array $configuredDecisions
     * @return mixed
     */
    public function decide(array $configuredDecisions, ComparatorInterface $comparator);

    public function getPossibleDecisions();

    public function __toString();
}
