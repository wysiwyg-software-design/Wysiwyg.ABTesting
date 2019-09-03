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
     * @param Feature $feature
     * @param string $forcedDecision
     * @return string | boolean
     * @Flow\Session(autoStart = TRUE)
     */
    public function getDecisionForFeature(Feature $feature, string $forcedDecision = '')
    {
        if ($forcedDecision) {
            return strtolower($forcedDecision);
        }

        return $this->decisionService->getDecisionForFeature($feature);
    }

    /**
     * Returns a decision for a feature by featureName.
     *
     * @param string $featureName
     * @param string $forcedDecision
     *
     * @return string|null
     */
    public function getDecisionForFeatureByName(string $featureName, string $forcedDecision = '')
    {
        $decision = null;
        $foundFeature = $this->featureRepository->findOneByFeatureName($featureName);

        if ($foundFeature instanceof Feature) {
            $decision = $this->getDecisionForFeature($foundFeature, $forcedDecision);
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
