<?php

namespace Wysiwyg\ABTesting\Controller\Module\AbTesting;

use Neos\Flow\Annotations as Flow;
use Neos\Neos\Controller\Module\AbstractModuleController;
use Wysiwyg\ABTesting\Domain\Dto\DeciderObject;
use Wysiwyg\ABTesting\Domain\Model\Decision;
use Wysiwyg\ABTesting\Domain\Model\Feature;
use Wysiwyg\ABTesting\Domain\Repository\DecisionRepository;
use Wysiwyg\ABTesting\Domain\Repository\FeatureRepository;
use Wysiwyg\ABTesting\Domain\Service\DecisionService;
use Wysiwyg\ABTesting\Domain\Service\FeatureService;

class FeatureModuleController extends AbstractModuleController
{
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
     * @var DecisionService
     */
    protected $decisionService;

    /**
     * @Flow\Inject
     * @var FeatureService
     */
    protected $featureService;

    public function indexAction()
    {
        $this->redirect('listFeatures');
    }

    public function newFeatureAction()
    {

    }

    /**
     * @param Feature $feature
     *
     * @throws \Neos\Flow\Mvc\Exception\StopActionException
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    public function createFeatureAction($feature)
    {
        $existingFeature = $this->featureRepository->findOneByFeatureName($feature->getFeatureName());

        if (is_null($existingFeature)) {
            $this->featureRepository->add($feature);
        }

        $this->redirect('listFeatures');
    }

    public function listFeaturesAction()
    {
        $allFeatures = $this->featureRepository->findAll();

        $this->view->assign('allFeatures', $allFeatures);
    }

    /**
     * @param Feature $feature
     *
     * @throws \Neos\Eel\Exception
     */
    public function showFeatureAction($feature)
    {
        $decisions = $this->decisionRepository->findByFeature($feature);
        $pages = $this->featureService->getPagesWithFeature($feature);

        $this->view->assignMultiple([
            'decisions' => $decisions,
            'feature' => $feature,
            'pages' => $pages
        ]);
    }

    /**
     * @param Feature $feature
     *
     * @throws \Neos\Eel\Exception
     */
    public function deleteFeatureAction($feature)
    {
        $pages = $this->featureService->getPagesWithFeature($feature);
        $deletable = count($pages) == 0;

        $this->view->assignMultiple([
            'feature' => $feature,
            'deletable' => $deletable,
            'pages' => $pages
        ]);
    }

    /**
     * @param Feature $feature
     *
     * @throws \Neos\Flow\Mvc\Exception\StopActionException
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    public function deleteFeatureConfirmedAction($feature)
    {
        $this->featureRepository->remove($feature);
        $decisions = $this->decisionRepository->findByFeature($feature);

        foreach ($decisions as $decision) {
            $this->decisionRepository->remove($decision);
        }

        $this->redirect('index');
    }

    /**
     * @param Feature $feature
     */
    public function editFeatureAction($feature)
    {
        $decisions = $this->decisionRepository->findByFeature($feature);

        $this->view->assignMultiple([
            'decisions' => $decisions,
            'feature' => $feature
        ]);
    }

    /**
     * @param Feature $feature
     *
     * @throws \Neos\Flow\Mvc\Exception\StopActionException
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    public function updateFeatureAction($feature)
    {
        if ($feature) {
            $this->featureRepository->update($feature);
        }

        $this->redirect('listFeatures');
    }

    /**
     * @param Feature $feature
     *
     * @throws \Neos\Flow\Mvc\Exception\StopActionException
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    public function toggleActiveAction($feature)
    {
        $feature->setActive(!$feature->isActive());

        $this->featureRepository->update($feature);
        $this->persistenceManager->persistAll();

        $this->redirect('listFeatures');
    }

    /**
     * @param Feature $feature
     */
    public function addDecisionToFeatureAction($feature)
    {
        $deciderObjects = $this->decisionService->getAllDeciderObjects();

        $assignableDeciderObjects = [];
        $deciderToIgnore = [];

        /** @var Decision $decision */
        foreach ($feature->getDecisions() as $decision) {
            $deciderToIgnore[] = $decision->getDecider();
        }

        /** @var DeciderObject $deciderObject */
        foreach ($deciderObjects as $deciderObject) {
            if (in_array($deciderObject->getDecider(), $deciderToIgnore)) {
                continue;
            }
            $assignableDeciderObjects[] = $deciderObject;
        }

        $this->view->assignMultiple([
            'feature' => $feature,
            'deciderObjects' => $assignableDeciderObjects
        ]);
    }

    /**
     * @param Decision $decision
     *
     * @throws \Neos\Flow\Mvc\Exception\StopActionException
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    public function saveDecisionToFeatureAction($decision)
    {
        $this->decisionRepository->add($decision);
        $this->redirect('listFeatures');
    }

    /**
     * @param Decision $decision
     */
    public function editDecisionAction($decision)
    {
        $this->view->assign('decision', $decision);
    }

    /**
     * @param Decision $decision
     *
     * @throws \Neos\Flow\Mvc\Exception\StopActionException
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    public function updateDecisionAction($decision)
    {
        $this->decisionRepository->update($decision);
        $this->redirect('listFeatures');
    }

    /**
     * @param Decision $decision
     */
    public function deleteDecisionAction($decision)
    {
        $this->view->assign('decision', $decision);
    }

    /**
     * @param Decision $decision
     *
     * @throws \Neos\Flow\Mvc\Exception\StopActionException
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    public function deleteDecisionConfirmedAction($decision)
    {
        $this->decisionRepository->remove($decision);
        $this->redirect('listFeatures');
    }
}
