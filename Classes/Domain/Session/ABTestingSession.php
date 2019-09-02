<?php

namespace Wysiwyg\ABTesting\Domain\Session;

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("session")
 */
class ABTestingSession
{
    /**
     * @var array
     */
    protected $decisions = array();

    /**
     * @param string $decision
     * @return void
     * @Flow\Session(autoStart = TRUE)
     */
    public function addItem($decision)
    {
        $this->decisions[] = $decision;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->decisions;
    }

    /**
     * @param $feature
     * @return string | null
     * @Flow\Session(autoStart = TRUE)
     */
    public function getDecisionForFeature($feature)
    {
        if (array_key_exists($feature, $this->decisions)) {
            return $this->decisions[$feature];
        }

        return null;
    }

    /**
     * @param $feature
     * @param $decision
     * @Flow\Session(autoStart = TRUE)
     */
    public function setDecisionForFeature($feature, $decision)
    {
        $this->decisions[$feature] = $decision;
    }
}
