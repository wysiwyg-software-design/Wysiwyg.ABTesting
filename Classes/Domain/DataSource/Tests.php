<?php

namespace Wysiwyg\ABTesting\Domain\DataSource;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Neos\Service\DataSource\AbstractDataSource;
use Neos\Neos\Service\DataSource\DataSourceInterface;
use Wysiwyg\ABTesting\Domain\Model\Feature;
use Wysiwyg\ABTesting\Domain\Repository\FeatureRepository;
use Neos\Flow\Annotations as Flow;

class Tests extends AbstractDataSource implements DataSourceInterface
{
    /**
     * @var string
     * @api
     */
    protected static $identifier = 'wysiwyg-abtesting-tests';

    /**
     * @Flow\Inject
     * @var FeatureRepository
     */
    protected $featureRepository;

    /**
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * Get data
     *
     * The return value must be JSON serializable data structure.
     *
     * @param NodeInterface $node The node that is currently edited (optional)
     * @param array $arguments Additional arguments (key / value)
     * @return mixed JSON serializable data
     * @api
     */
    public function getData(NodeInterface $node = null, array $arguments = [])
    {
        return $this->getMappedFeaturesToSelectOptions();
    }

    protected function getMappedFeaturesToSelectOptions()
    {
        $allFeatures = $this->featureRepository->findAll();

        $mappedFeatures = [];

        /**
         * @var Feature $feature
         */
        foreach ($allFeatures as $feature) {
            $mappedFeatures[] = [
                'label' => $feature->getFeatureName(),
                'value' => $this->persistenceManager->getIdentifierByObject($feature),
                'icon' => 'icon-cog'
            ];
        }

        return $mappedFeatures;
    }
}
