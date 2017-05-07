<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.04.17
 */

namespace Dopamedia\ProductCompleteness\Model\Indexer\Completeness\Action;

use Dopamedia\ProductCompleteness\Model\Indexer\Completeness\AbstractAction;

class Rows extends AbstractAction
{
    /**
     * @TODO::currently a full reindex gets performed on every save operation => implement logic to process only some entities
     * @inheritDoc
     */
    public function execute(array $entityIds = [], $useTempTable = false): AbstractAction
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