<?php

namespace Wysiwyg\ABTesting\Eel;

use Neos\Eel\ProtectedContextAwareInterface;
use Neos\Flow\Annotations as Flow;
use Wysiwyg\ABTesting\Domain\Model\Feature;
use Wysiwyg\ABTesting\Domain\Repository\FeatureRepository;

class FeatureHelper implements ProtectedContextAwareInterface
{
    /**
     * @Flow\Inject
     * @var FeatureRepository
     */
    protected $featureRepository;

    /**
     * Returns a feature found by it's Id.
     *
     * @param string $featureId
     *
     * @return Feature|null
     */
    public function getFeatureById(string $featureId):?Feature
    {
        if ($featureId) {
            return $this->featureRepository->findByIdentifier($featureId);
        }

        return null;
    }

    /**
     * @param string $methodName
     * @return boolean
     */
    public function allowsCallOfMethod($methodName)
    {
        switch ($methodName) {
            case 'getFeatureById':
                return true;
        }

        return false;
    }
}
