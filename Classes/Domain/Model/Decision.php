<?php

namespace Wysiwyg\ABTesting\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;
use Wysiwyg\ABTesting\Domain\Decider\DeciderInterface;

/**
 * @Flow\Entity
 */
class Decision
{
    /**
     * @var string
     */
    protected $deciderClassName;

    /**
     * @var Feature
     * @ORM\ManyToOne(inversedBy="decisions")
     */
    protected $feature;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    protected $decision = [];

    /**
     * @return string
     */
    public function getDeciderClassName(): string
    {
        return $this->deciderClassName;
    }

    /**
     * @param string $deciderClassName
     */
    public function setDeciderClassName(string $deciderClassName): void
    {
        $this->deciderClassName = $deciderClassName;
    }

    /**
     * @return Feature
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * @param Feature $feature
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;
    }

    /**
     * @return array
     */
    public function getDecision()
    {
        return $this->decision;
    }

    /**
     * @param array $decision
     */
    public function setDecision($decision)
    {
        $this->decision = $decision;
    }

    /**
     * @return DeciderInterface
     */
    public function getDecider()
    {
        return new $this->deciderClassName;
    }
}
