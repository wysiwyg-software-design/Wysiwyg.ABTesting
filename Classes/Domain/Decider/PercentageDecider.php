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
     * Also supports multiple decisions, for example c will be added
     * Weighted decision a(40%) b (40%) c (20%)
     *
     * [
     *        'a' => 40,
     *        'b' => 40,
     *        'c' => 20
     * ]
     *
     * @param array $decisions
     * @return null|string
     */
    public function decide(array $decisions) : string
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
