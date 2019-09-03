<?php

namespace Wysiwyg\ABTesting\Domain\Decider;

class PercentageDecider implements DeciderInterface
{
    /**
     * Returns a decision from a given array.
     * Decides by weight given from array values.
     *
     * Weighted decision: a(60%) b (40%)
     * [
     *        'a' => 60,
     *        'b' => 40
     *  ]
     *
     * @param array $decisions
     * @return string|null
     */
    public function decide(array $decisions)
    {
        $randomValue = rand(1, 100);
        $tempDecision = 0;

        foreach ($decisions as $key => $decision) {
            $tempDecision += $decision;
            if ($tempDecision >= $randomValue) {
                return $key;
            }
        }

        return null;
    }
}
