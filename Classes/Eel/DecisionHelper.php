<?php

namespace Wysiwyg\ABTesting\Eel;

use Neos\Eel\ProtectedContextAwareInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Wysiwyg\ABTesting\Domain\Model\Feature;
use Wysiwyg\ABTesting\Domain\Repository\FeatureRepository;
use Wysiwyg\ABTesting\Domain\Service\DecisionService;

class DecisionHelper implements ProtectedContextAwareInterface
{
    /**
     * @Flow\Inject
     * @var DecisionService
     */
    protected $decisionService;

    /**
     * @Flow\Inject
     * @var FeatureRepository
     */
    protected $featureRepository;

    /**
     * Returns a calculated decision-string for given feature.
     *
     * @Flow\Session(autoStart = true)
     * @param string $featureIdentifier
     * @param string $forceABVersion
     *
     * @return string|null
     */
    public function getDecisionForFeature(string $featureIdentifier = null, string $forceABVersion = null)
    {
        if ($featureIdentifier === null) {
            return null;
        }

        $feature = $this->featureRepository->findByIdentifier($featureIdentifier);

        if ($feature === null) {
            return null;
        }


        if ($forceABVersion) {
            return strtolower($forceABVersion);
        }

        return $this->decisionService->getDecisionForFeature($feature);
    }

    /**
     * Returns a decision for a feature by featureName.
     *
     * @param string $featureName
     * @param string $forceABVersion
     *
     * @return string|null
     */
    public function getDecisionForFeatureByName(string $featureName, string $forceABVersion = null)
    {
        $decision = null;
        $foundFeature = $this->featureRepository->findOneByFeatureName($featureName);

        if ($foundFeature instanceof Feature) {
            $decision = $this->getDecisionForFeature($foundFeature, $forceABVersion);
        }

        return $decision;
    }

    /**
     * @return array
     */
    public function getAllDecisions()
    {
        return $this->decisionService->decideForAllFeatures();
    }

    /**
     * @param string $methodName
     *
     * @return boolean
     */
    public function allowsCallOfMethod($methodName)
    {
        switch ($methodName) {
            case 'getDecisionForFeature':
                return true;
            case 'getAllDecisions':
                return true;
            case 'getDecisionForFeatureByName':
                return true;
        }

        return false;
    }
}
