<?php

namespace Wysiwyg\ABTesting\Domain\Factory;

use Wysiwyg\ABTesting\Domain\Decider\DeciderInterface;
use Wysiwyg\ABTesting\Domain\Decider\PercentageDecider;

class DeciderFactory
{
    /**
     * Array of all Deciders for A/B Testing.
     * When new Deciders are added, they must be added in this array aswell.
     *
     * @var array
     */
    private $deciders = [
        PercentageDecider::class
    ];

    /**
     * Gets an Instance of a Decider by Class name - since every Decider implements an
     * DeciderInterface, return type was set to DeciderInterface
     *
     * @param $className
     * @return DeciderInterface
     */
    public function getDecider($className)
    {
        foreach ($this->deciders as $decider) {
            if ($className === $decider) {
                return new $decider;
            }
        }

        return null;
    }

    public function getAllDeciders()
    {
        return $this->deciders;
    }
}
