<?php

namespace Wysiwyg\ABTesting\Domain\Repository;

use Neos\Flow\Persistence\Repository;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class FeatureRepository extends Repository
{

    /**
     * @return array
     */
    public function getAllActiveFeatures()
    {
        $flowQuery = $this->createQuery();

        $flowQuery->matching($flowQuery->equals('active', 1));

        return $flowQuery->execute()->toArray();
    }
}
