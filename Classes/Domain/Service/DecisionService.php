<?php

namespace Wysiwyg\ABTesting\Domain\Service;

use Neos\Flow\Annotations as Flow;
use Wysiwyg\ABTesting\Domain\Decider\DeciderInterface;
use Wysiwyg\ABTesting\Domain\Dto\DeciderObject;
use Wysiwyg\ABTesting\Domain\Factory\DeciderFactory;
use Wysiwyg\ABTesting\Domain\Model\Decision;
use Wysiwyg\ABTesting\Domain\Model\Feature;
use Wysiwyg\ABTesting\Domain\Repository\DecisionRepository;
use Wysiwyg\ABTesting\Domain\Repository\FeatureRepository;
use Wysiwyg\ABTesting\Domain\Session\ABTestingSession;

class DecisionService
{

    /**
     * @Flow\Inject
     * @var DeciderFactory
     */
    protected $deciderFactory;

    /**
     * @Flow\Inject
     * @var FeatureRepository
     */
    protected $featureRepository;

    /**
     * @Flow\Inject
     * @var DecisionRepository
     */
    protected $decisionRepository;

    /**
     * @Flow\Inject
     * @var ABTestingSession
     */
    protected $abTestingSession;

    /**
     * Returns a decision for AB Testing from a Feature.
     * Returned value will always be a string, for example:
     * 'a' or 'b'.
     * It's possible that the feature has more than 'a' or 'b' decision, which will also be returned.
     *
     * Since the decision is saved in Session, the session decision is leading and will be returned, if a decision
     * is already saved in Session.
     *
     * @Flow\Session(autoStart = true)
     * @param Feature $feature
     *
     * @return string|null
     */
    public function getDecisionForFeature($feature)
    {
        if (!$feature->isActive()) {
            return false;
        }

        $featureName = $feature->getFeatureName();
        $decisionFromCookie = $this->getDecisionFromCookies($featureName);
        $decisionFromSession = $this->abTestingSession->getDecisionForFeature($featureName);
        $decision = $decisionFromCookie ?: $decisionFromSession;

        if ($decision) {
            return $decision;
        }

        $decisionsForFeature = $this->decisionRepository->findByFeature($feature);
        /**  @var Decision $singleDecision */
        foreach ($decisionsForFeature as $singleDecision) {
            $decider = $singleDecision->getDecider();

            if (!$decider instanceof DeciderInterface) {
                return null;
            }

            $decision = $decider->decide($singleDecision->getDecision());
            if (!$decision) {
                return null;
            }
        }

        $this->abTestingSession->setDecisionForFeature($featureName, $decision);

        return $decision;
    }

    /**
     * Returns all Decider Class Names without any Namespaces.
     * @return array
     */
    public function getAllDeciderObjects()
    {
        $deciderObjects = [];

        foreach ($this->deciderFactory->getAllDeciders() as $deciderClassName) {
            $lastSlashPosition = strrpos($deciderClassName, '\\');
            $deciderName = substr($deciderClassName, $lastSlashPosition + 1);

            $deciderObject = new DeciderObject();
            $deciderObject->setDecider($deciderClassName);
            $deciderObject->setDeciderName($deciderName);

            $deciderObjects[] = $deciderObject;
        }

        return $deciderObjects;
    }

    /**
     * Decides for every features which are configured in database.
     */
    public function decideForAllFeatures()
    {
        $features = $this->featureRepository->findAll();
        $decisions = [];

        /** @var Feature $feature */
        foreach ($features as $feature) {
            if ($feature->isActive() && count($feature->getDecisions()) > 0) {
                $featureName = str_replace(' ', '_', $feature->getFeatureName());
                $decisions[$featureName] = $this->getDecisionForFeature($feature);
            }
        }

        return $decisions;
    }

    /**
     * @param $featureName
     * @param $decisionOverride
     */
    public function forceDecisionOverrideForFeature($featureName, $decisionOverride)
    {
        $featureRepository = new FeatureRepository();
        $feature = $featureRepository->findOneByFeatureName($featureName);

        if ($feature instanceof Feature) {
            $this->abTestingSession->setDecisionForFeature($featureName, $decisionOverride);
        }
    }

    /**
     * @param string $featureName
     * @return string|null
     */
    public function getDecisionFromCookies($featureName)
    {
        if(!array_key_exists('WYSIWYG_AB_TESTING', $_COOKIE)){
            return null;
        }
        $decisionsArray = json_decode($_COOKIE['WYSIWYG_AB_TESTING'], true);

        return array_key_exists($featureName, $decisionsArray) ? $decisionsArray[$featureName] : null;
    }
}
