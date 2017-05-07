<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.04.17
 */

namespace Dopamedia\ProductCompleteness\Model\Indexer\Completeness\Action;

use Dopamedia\ProductCompleteness\Model\Indexer\Completeness\AbstractAction;

class Full extends AbstractAction
{
    /**
     * @param array|null $ids
     * @return AbstractAction
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(array $ids = null): AbstractAction
    {
        try {
            foreach ($this->storeManager->getStores() as $store) {
                $this->reindexByStore($store->getId());
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
        return $this;
    }
}