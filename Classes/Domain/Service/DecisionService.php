<?php

namespace Wysiwyg\ABTesting\Domain\Service;

use Neos\Flow\Annotations as Flow;
use Wysiwyg\ABTesting\Domain\Comparator\ComparatorInterface;
use Wysiwyg\ABTesting\Domain\Dto\DeciderObject;
use Wysiwyg\ABTesting\Domain\Factory\DeciderFactory;
use Wysiwyg\ABTesting\Domain\Model\Decision;
use Wysiwyg\ABTesting\Domain\Model\Feature;
use Wysiwyg\ABTesting\Domain\Repository\DecisionRepository;
use Wysiwyg\ABTesting\Domain\Repository\FeatureRepository;

/**
 * The main access point to ask for feature decisions in code.
 */
class DecisionService
{
    /**
     * @Flow\InjectConfiguration(path="cookie")
     * @var array
     */
    protected $cookieSettings;

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
     * @Flow\InjectConfiguration( package="Wysiwyg.AbTesting", path="comparatorClassName")
     * @var array
     */
    protected $configuredComparatorClass;

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
     */
    public function getDecisionForFeature(Feature $feature): string
    {
        $featureName = $feature->getFeatureName();
        $decisionFromCookie = $this->getDecisionFromCookies($featureName);
        $decisionsForFeature = $this->decisionRepository->findByFeature($feature);

        if ($decisionFromCookie) {
            return $decisionFromCookie;
        }

        // We want to reuse the same comparator here
        $comparator = $this->getComparator();
        $decision = '';

        /**
         * @var Decision $singleDecision
         */
        foreach ($decisionsForFeature as $singleDecision) {
            $decider = $singleDecision->getDecider();
            $decision = $decider->decide($singleDecision->getDecision(), $comparator);

            if (!$decision) {
                return '';
            }
        }

        return $decision;
    }

    /**
     * Returns all Decider Class Names without any Namespaces.
     *
     * @return array
     */
    public function getAllDeciderObjects(): array
    {
        $featureFactory = new DeciderFactory();

        $deciderObjects = [];

        foreach ($featureFactory->getAllDecider() as $deciderClassName) {
            $lastSlashPosition = strrpos($deciderClassName, '\\');
            $deciderName = substr($deciderClassName, $lastSlashPosition + 1);

            $deciderObject = new DeciderObject();
            $deciderObject->setDeciderClass($deciderClassName);
            $deciderObject->setDeciderName($deciderName);

            $deciderObjects[] = $deciderObject;
        }

        return $deciderObjects;
    }

    /**
     * Decides for all features which are configured in database.
     *
     * @return string[]
     */
    public function decideForAllFeatures(): array
    {
        $features = $this->featureRepository->findAll();

        $decisions = [];

        /**
         * @var Feature $feature
         */
        foreach ($features as $feature) {
            if ($feature->isActive()) {

                $featureName = str_replace(' ', '_', $feature->getFeatureName());

                $decisions[$featureName] = $this->getDecisionForFeature($feature);
            }
        }

        return $decisions;
    }

    /**
     * @param string $featureName
     * @return string
     */
    public function getDecisionFromCookies(string $featureName): string
    {
        $cookieName = $this->cookieSettings['name'] ?? 'WYSIWYG_AB_TESTING';

        if (array_key_exists($cookieName, $_COOKIE)) {
            $decisionsArray = json_decode($_COOKIE[$cookieName], true);

            return array_key_exists($featureName, $decisionsArray) ? $decisionsArray[$featureName] : '';
        }

        return '';
    }

    /**
     * Create the comparator to be used for decisions. Can be configured
     * TODO: Should probably be a factory that can be injected
     *
     * @return ComparatorInterface
     */
    protected function getComparator(): ComparatorInterface
    {
        return new $this->configuredComparatorClass;
    }
}
