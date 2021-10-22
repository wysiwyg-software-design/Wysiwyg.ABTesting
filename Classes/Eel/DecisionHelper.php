<?php

namespace Wysiwyg\ABTesting\Eel;

use Neos\Eel\ProtectedContextAwareInterface;
use Neos\Flow\Annotations as Flow;
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
     * Returns a calculated decision-string for given Test by Name
     *
     * @param Feature $feature
     * @param string $forcedDecision
     * @return string
     */
    public function getDecisionForFeature(Feature $feature, $forcedDecision = null)
    {
        if ($forcedDecision) {
            return $forcedDecision;
        }

        return $this->decisionService->getDecisionForFeature($feature);
    }

    /**
     * Returns a decision for a Feature by featureName.
     *
     * @param string $featureName
     * @param string $forcedDecision
     * @return string
     */
    public function getDecisionForFeatureByName(string $featureName, $forcedDecision = null)
    {
        if ($forcedDecision) {
            return $forcedDecision;
        }

        $foundFeature = $this->featureRepository->findOneByFeatureName($featureName);
        return ($foundFeature instanceof Feature) ? $this->getDecisionForFeature($foundFeature, $forcedDecision) : '';
    }
    /**
     * Returns a decision for a Feature by featureName.
     *
     * @param string $featurePersistentIdentifier
     * @param string $forcedDecision
     * @return string
     */
    public function getDecisionForFeatureByIdentifier(string $featurePersistentIdentifier, $forcedDecision = null)
    {
        if ($forcedDecision) {
            return $forcedDecision;
        }

        $foundFeature = $this->featureRepository->findByIdentifier($featurePersistentIdentifier);
        return ($foundFeature instanceof Feature) ? $this->getDecisionForFeature($foundFeature, $forcedDecision) : '';
    }

    /**
     * @return string[]
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
            case 'getAllDecisions':
            case 'getDecisionForFeatureByName':
            case 'getDecisionForFeatureByIdentifier':
                return true;
        }

        return false;
    }
}
