<?php
/**
 * User: sven <wuetherich@wysiwyg.de>
 * Date: 02.07.2018
 */

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



}
