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
     * @param Feature $feature
     * @return string
     * @Flow\Session(autoStart = TRUE)
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

        /**
         * @var Decision $singleDecision
         */
        foreach ($decisionsForFeature as $singleDecision) {
            $decider = $this->deciderFactory->getDecider($singleDecision->getDecider());

            if (!$decider instanceof DeciderInterface) {
                return false;
            }

            $decision = $decider->decide($singleDecision->getDecision());
            if (!$decision) {
                return false;
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
     * Decisions are made by priority.
     *
     * @todo This should actually make a decision-chaining which is currently not supported.
     *
     * @param $decisions
     * @return array|string
     * @deprecated still in progress - do not use yet.
     */
    private function decideByLeading($decisions)
    {
        $tempDecision = [];
        $decision = '';

        $factory = $this->deciderFactory;

        /**
         * @var Decision $decisionItem
         */
        foreach ($decisions as $decisionItem) {
            $decider = $factory->getDecider($decisionItem->getDecider());
            $decisionFromDecider = $decider->decide($decisionItem->getDecision());

            if (is_array($decisionFromDecider)) {
                $tempDecision = $decisionFromDecider;
            }

            if (in_array($decisionFromDecider, $tempDecision)) {
                $decision = $decisionFromDecider;
            }
        }

        return $decision;
    }

    /**
     * Decides for every features which are configured in database.
     */
    public function decideForAllFeatures()
    {
        $features = $this->featureRepository->findAll();

        $decisions = [];

        /**
         * @var Feature $feature
         */
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
     * @param $decision
     */
    public function forceDecisionOverrideForFeature($featureName, $decision)
    {
        $featureRepository = new FeatureRepository();

        $feature = $featureRepository->findOneByFeatureName($featureName);

        if ($feature instanceof Feature) {
            $this->abTestingSession->setDecisionForFeature($featureName, $decision);
        }

    }

    /**
     * @param string $featureName
     * @return string | bool
     */
    public function getDecisionFromCookies($featureName)
    {
        if (array_key_exists('WYSIWYG_AB_TESTING', $_COOKIE)) {
            $decisionsArray = json_decode($_COOKIE['WYSIWYG_AB_TESTING'], true);

            return array_key_exists($featureName, $decisionsArray) ? $decisionsArray[$featureName] : false;
        }
    }
}
