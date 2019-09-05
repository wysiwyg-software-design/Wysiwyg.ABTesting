<?php

namespace Wysiwyg\ABTesting\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;
use Wysiwyg\ABTesting\Domain\Decider\DeciderInterface;

/**
 * @Flow\Entity
 */
class Feature
{
    /**
     * @var string
     */
    protected $featureName;

    /**
     * @var boolean
     */
    protected $active = false;

    /**
     * @ORM\OneToMany(mappedBy="feature")
     * @var ArrayCollection<Wysiwyg\ABTesting\Domain\Model\Decision>
     */
    protected $decisions;

    /**
     * @ORM\Column(nullable=true)
     * @var string
     */
    protected $defaultDecision = null;

    /**
     * @return string
     */
    public function getFeatureName()
    {
        return $this->featureName;
    }

    /**
     * @param string $featureName
     */
    public function setFeatureName($featureName)
    {
        $this->featureName = $featureName;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return ArrayCollection
     */
    public function getDecisions()
    {
        return $this->decisions;
    }

    /**
     * @param ArrayCollection $decisions
     */
    public function setDecisions($decisions)
    {
        $this->decisions = $decisions;
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
}
