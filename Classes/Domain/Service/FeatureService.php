<?php

namespace Wysiwyg\ABTesting\Domain\Service;

use Neos\ContentRepository\Domain\Model\Node;
use Neos\ContentRepository\Domain\Service\ContextFactoryInterface;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Neos\Service\LinkingService;
use Wysiwyg\ABTesting\Domain\Repository\FeatureRepository;

class FeatureService
{
    /**
     * @Flow\Inject
     * @var ContextFactoryInterface
     */
    protected $contextFactory;

    /**
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @Flow\Inject
     * @var LinkingService
     */
    protected $linkingService;

    /**
     * @Flow\Inject
     * @var FeatureRepository
     */
    protected $featureRepository;

    /**
     * Finds all document nodes which includes the given features.
     * Currently it only get's the parent of the parent of the container which is always the documentNode.
     *
     * NodeTree must apply the following structure:
     *  DocumentNode (parent)
     *      ContentCollection (parent)
     *          ABTestingContainer
     *
     * @param $feature
     * @return array
     * @throws \Neos\Eel\Exception
     */
    public function getPagesWithFeature($feature)
    {
        $flowQuery = new FlowQuery([$this->contextFactory->create()]);
        $currentSiteNode = $flowQuery->get(0)->getCurrentSiteNode();

        $q = new FlowQuery([$currentSiteNode]);

        $featureId = $this->persistenceManager->getIdentifierByObject($feature);
        $foundContainer = $q->find('[instanceof Wysiwyg.ABTesting:ABTestingContainer][abTest][abTest="' . $featureId . '"]')->get();

        $pageNodes = [];

        /**
         * @var Node $container
         */
        foreach ($foundContainer as $container) {
            $parent = $container->getParent()->getParent();
            $pageNodes[] = $parent;
        }

        return $pageNodes;

    }

    /**
     * Wrapper method to get allActiveFeatures.
     *
     * @return array
     */
    public function getAllActiveFeatures()
    {
        return $this->featureRepository->getAllActiveFeatures();
    }

}
