<?php
/**
 * User: sven <wuetherich@wysiwyg.de>
 * Date: 02.07.2018
 */

namespace Wysiwyg\ABTesting\Domain\Model;

use Wysiwyg\ABTesting\Domain\Decider\DeciderInterface;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Entity
 */
class Decision
{

    /**
     * @var DeciderInterface
     */
    protected $decider;

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
     * @var string
     */
    protected $defaultDecision;

    /**
     * @var integer
     */
    protected $priority;

    /**
     * @return DeciderInterface
     */
    public function getDecider()
    {
        return new $this->decider;
    }

    /**
     * @param DeciderInterface $decider
     */
    public function setDecider($decider)
    {
        $this->decider = $decider;
    }

    /**
     * @return string
     */
    public function getDefaultDecision()
    {
        return $this->defaultDecision;
    }

    /**
     * @param string $defaultDecision
     */
    public function setDefaultDecision($defaultDecision)
    {
        $this->defaultDecision = $defaultDecision;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
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
}
