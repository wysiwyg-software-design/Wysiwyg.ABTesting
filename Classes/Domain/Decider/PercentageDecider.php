<?php

namespace Wysiwyg\ABTesting\Domain\Decider;

use Wysiwyg\ABTesting\Domain\Comparator\ComparatorInterface;

class PercentageDecider implements DeciderInterface
{
    protected $possibleDecisions = ['a' => 'a', 'b' => 'b'];

    /**
     * Returns a decision from a given array.
     * Decides by weight given from array values.
     *
     * Weighted Decision: a(60%) b (40%)
     * [
     *        'a' => 60,
     *        'b' => 40
     *  ]
     *
     * Also supports multiple decisions, for example c will be added:
     * Weighted Decision: a(40%) b (40%) c (20%)
     *
     * [
     *        'a' => 40,
     *        'b' => 40,
     *        'c' => 20
     * ]
     *
     * @param array $decisions
     * @param ComparatorInterface $comparator
     * @return null|string
     */
    public function decide(array $decisions, ComparatorInterface $comparator)
    {
        $comparisonValue = $comparator->getComparisonValue();

        $tempDecision = 0;

        foreach ($decisions as $key => $decision) {
            $tempDecision += $decision;
            if ($tempDecision >= $comparisonValue) {
                return $key;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getPossibleDecisions()
    {
        return $this->possibleDecisions;
    }

    public function getTitle()
    {
        return 'PercentageDecider';
    }

    public function __toString()
    {
        return PercentageDecider::class;
    }
}
