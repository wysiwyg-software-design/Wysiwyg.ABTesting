<?php

namespace Wysiwyg\ABTesting\Domain\Dto;

use Wysiwyg\ABTesting\Domain\Decider\DeciderInterface;

class DeciderObject
{
    /**
     * @var string
     */
    protected $deciderName;

    /**
     * @var DeciderInterface
     */
    protected $decider;

    /**
     * @var string
     */
    protected $conditionA;

    /**
     * @var string
     */
    protected $conditionB;

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
     * @return DeciderInterface
     */
    public function getDecider()
    {
        return $this->decider;
    }

    /**
     * @param DeciderInterface $deciderClass
     */
    public function setDecider($deciderClass)
    {
        $this->decider = $deciderClass;
    }

    /**
     * @return string
     */
    public function getConditionA()
    {
        return $this->conditionA;
    }

    /**
     * @param string $conditionA
     */
    public function setConditionA($conditionA)
    {
        $this->conditionA = $conditionA;
    }

    /**
     * @return string
     */
    public function getConditionB()
    {
        return $this->conditionB;
    }

    /**
     * @param string $conditionB
     */
    public function setConditionB($conditionB)
    {
        $this->conditionB = $conditionB;
    }
}
