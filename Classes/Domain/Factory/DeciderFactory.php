<?php

namespace Wysiwyg\ABTesting\Domain\Factory;

use Neos\Flow\Annotations as Flow;
use Wysiwyg\ABTesting\Domain\Decider\DeciderInterface;

class DeciderFactory
{
    /**
     * Array of all Deciders for A/B Testing.
     * When new Deciders are added, they must be added in this array aswell.
     *
     * @var array
     */
    private $testDecider = [];

    /**
     * @Flow\InjectConfiguration( package="Wysiwyg.AbTesting", path="deciders")
     * @var array
     */
    protected $deciderSettings;

    public function initializeObject()
    {
        $enabledDeciders = array_filter($this->deciderSettings, function ($element) {
            return (array_key_exists('enabled', $element) && $element['enabled']);
        });

        foreach ($enabledDeciders as $enabledDecider => $enabledValue) {
            if (class_exists('Wysiwyg\ABTesting\Domain\Decider\\' . $enabledDecider)) {
                $this->testDecider[] = 'Wysiwyg\ABTesting\Domain\Decider\\' . $enabledDecider;
            }
        }
    }

    /**
     * Gets an Instance of a Decider by Class name - since every Decider implements an
     * DeciderInterface, return type was set to DeciderInterface
     *
     * @param $className
     * @return DeciderInterface
     */
    public function getTestDecider($className)
    {
        foreach ($this->testDecider as $decider) {
            if ($className === $decider) {
                return new $decider;
            }
        }

        return null;
    }

    public function getAllDecider()
    {
        return $this->testDecider;
    }
}
