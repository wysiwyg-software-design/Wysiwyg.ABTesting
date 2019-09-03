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
     * @Flow\Session(autoStart = TRUE)
     * @param string $decision
     *
     * @return void
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
     * @Flow\Session(autoStart = TRUE)
     * @param $feature
     *
     * @return string | null
     */
    public function getDecisionForFeature($feature)
    {
        if (array_key_exists($feature, $this->decisions)) {
            return $this->decisions[$feature];
        }

        return null;
    }

    /**
     * @Flow\Session(autoStart = TRUE)
     *
     * @param $feature
     * @param $decision
     */
    public function setDecisionForFeature($feature, $decision)
    {
        $this->decisions[$feature] = $decision;
    }
}
