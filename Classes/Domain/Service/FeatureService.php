<?php
/**
 * User: sven <wuetherich@wysiwyg.de>
 * Date: 02.07.2018
 */

namespace Wysiwyg\ABTesting\Domain\Service;

use Neos\ContentRepository\Domain\Model\Node;
use Neos\ContentRepository\Domain\Service\ContextFactoryInterface;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Neos\Service\LinkingService;
use Wysiwyg\ABTesting\Domain\Model\Feature;
use Wysiwyg\ABTesting\Domain\Repository\FeatureRepository;
use Neos\Flow\Annotations as Flow;

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
     * @param Feature $feature
     * @return array
     * @throws \Neos\Eel\Exception
     */
    public function getPagesWithFeature(Feature $feature): array
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
     * @return array
     */
    public function getAllActiveFeatures()
    {
        return $this->featureRepository->getAllActiveFeatures();

    }

}
