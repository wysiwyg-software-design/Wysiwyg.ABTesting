<?php

namespace Wysiwyg\ABTesting\Domain\Dto;

use Wysiwyg\ABTesting\Domain\Model\Feature;

class DeciderObject
{

    /**
     * @var string
     */
    protected $deciderName;

    /**
     * @var string
     */
    protected $deciderClass;

    /**
     * @var Feature
     */
    protected $feature;

    /**
     * @return string
     */
    public function getDeciderName()
    {
        return $this->deciderName;
    }

    /**
     * @param string $deciderName
     */
    public function setDeciderName($deciderName)
    {
        $this->deciderName = $deciderName;
    }

    /**
     * @return string
     */
    public function getDeciderClass()
    {
        return $this->deciderClass;
    }

    /**
     * @param string $deciderClass
     */
    public function setDeciderClass($deciderClass)
    {
        $this->deciderClass = $deciderClass;
    }

    /**
     * @return Feature
     */
    public function getFeature(): Feature
    {
        return $this->feature;
    }

    /**
     * @param Feature $feature
     */
    public function setFeature(Feature $feature): void
    {
        $this->feature = $feature;
    }
}
