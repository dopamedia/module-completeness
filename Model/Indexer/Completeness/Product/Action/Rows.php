<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.04.17
 */

namespace Dopamedia\Completeness\Model\Indexer\Completeness\Product\Action;

use Dopamedia\Completeness\Model\Indexer\Completeness\Product\AbstractAction;

class Rows extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function execute(array $entityIds = [])
    {
        try {
            foreach ($this->storeManager->getStores(true) as $store) {
                $this->reindexByStore($store->getId(), $entityIds);
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
        return $this;
    }

}