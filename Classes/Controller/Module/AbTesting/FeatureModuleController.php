<?php

namespace Wysiwyg\ABTesting\Controller\Module\AbTesting;

use Neos\Error\Messages as Error;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Property\TypeConverter\ObjectConverter;
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
     */
    public function showFeatureAction($feature)
    {
        if ($feature) {
            $decisions = $this->decisionRepository->findByFeature($feature);
            $pages = $this->featureService->getPagesWithFeature($feature);

            $this->view->assignMultiple([
                'decisions' => $decisions,
                'feature' => $feature,
                'pages' => $pages
            ]);
        }
    }

    /**
     * @param Feature $feature
     */
    public function deleteFeatureAction($feature)
    {
        $pages = $this->featureService->getPagesWithFeature($feature);
        $deletable = true;

        if (count($pages) > 0) {
            $deletable = false;
        }

        $this->view->assignMultiple([
            'feature' => $feature,
            'deletable' => $deletable,
            'pages' => $pages
        ]);
    }

    /**
     * @param Feature $feature
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
        if ($feature) {
            $decisions = $this->decisionRepository->findByFeature($feature);

            $this->view->assignMultiple([
                'decisions' => $decisions,
                'feature' => $feature
            ]);
        }
    }

    /**
     * @param Feature $feature
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
     */
    public function toggleActiveAction($feature)
    {
        if ($feature->isActive()) {
            $feature->setActive(false);
        } else {
            $feature->setActive(true);
        }

        $this->featureRepository->update($feature);
        $this->persistenceManager->persistAll();

        $this->redirect('listFeatures');
    }

    /**
     * @param Feature $feature
     */
    public function showPagesAction($feature)
    {
        $pages = $this->featureService->getPagesWithFeature($feature);
        $this->view->assignMultiple([
            'feature' => $feature,
            'pages' => $pages
        ]);
    }

    /**
     * @param Feature $feature
     */
    public function chooseDeciderAction($feature)
    {
        $deciderObjectsRaw = $this->decisionService->getAllDeciderObjects();

        $assignableDeciderObjects = [];
        $deciderToIgnore = [];

        /**
         * @var Decision $decision
         */
        foreach ($feature->getDecisions() as $decision) {
            $deciderToIgnore[] = $decision->getDecider();
        }

        /**
         * @var DeciderObject $deciderObject
         */
        foreach ($deciderObjectsRaw as $deciderObject) {
            if (!in_array($deciderObject->getDeciderName(), $deciderToIgnore)) {
                $assignableDeciderObjects[] = $deciderObject;
            }
        }

        $this->view->assignMultiple([
            'feature' => $feature,
            'deciderObjects' => $assignableDeciderObjects
        ]);
    }

    /**
     * @param DeciderObject $decider
     */
    public function addDecisionToFeatureAction($decider)
    {
        $deciderClass = $decider->getDeciderClass();
        $deciderObject = new $deciderClass;

        $this->view->assignMultiple([
            'feature' => $decider->getFeature(),
            'decider' => $deciderObject,
            'deciderClass' => $deciderClass
        ]);
    }

    /**
     * we need to allow the property mapper to override the decider class name from the request to map correctly for "saveDecisionToFeature".
     */
    public function initializeSaveDecisionToFeatureAction()
    {
        /* @var $propertyMappingConfiguration \Neos\Flow\Property\PropertyMappingConfiguration */
        $propertyMappingConfiguration = $this->arguments['decision']->getPropertyMappingConfiguration();
        $propertyMappingConfiguration->forProperty('decider')->setTypeConverterOption(ObjectConverter::class, ObjectConverter::CONFIGURATION_OVERRIDE_TARGET_TYPE_ALLOWED, true);
    }

    /**
     * @param Decision $decision
     */
    public function saveDecisionToFeatureAction($decision)
    {
        $decision->setPriority(0);
        $this->decisionRepository->add($decision);
        $this->addFlashMessage('Decider has been added.');
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
     * @throws \Neos\Flow\Mvc\Exception\StopActionException
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    public function updateDecisionAction($decision)
    {
        $total = 0;
        foreach ($decision->getDecision() as $version => $percentage) {
            $total += $percentage;
        }

        if ($total !== 100) {
            $this->addFlashMessage('Please configure 100% in total for all versions.', '', Error\Message::SEVERITY_ERROR);
            $this->redirect('editDecision', null, null, ['decision' => $decision]);
        }

        $this->addFlashMessage('A/B Test successfully configured.');
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
     */
    public function deleteDecisionConfirmedAction($decision)
    {
        $this->decisionRepository->remove($decision);
        $this->addFlashMessage('Decision has been deleted.', '', Error\Message::SEVERITY_NOTICE);
        $this->redirect('listFeatures');
    }
}
