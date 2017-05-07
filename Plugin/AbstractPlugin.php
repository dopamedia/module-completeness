<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.04.17
 */

namespace Dopamedia\ProductCompleteness\Plugin;


use Dopamedia\ProductCompleteness\Model\Indexer\Completeness;

class AbstractPlugin
{
    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     */
    public function __construct(
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
    ) {
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * @param int $productId
     * @return void
     */
    protected function reindexRow(int $productId)
    {
        $indexer = $this->indexerRegistry->get(Completeness::INDEXER_ID);
        if (!$indexer->isScheduled()) {
            $indexer->reindexRow($productId);
        }
    }

    /**
     * @param int[] $productIds
     * @return void
     */
    protected function reindexList(array $productIds)
    {
        $indexer = $this->indexerRegistry->get(Completeness::INDEXER_ID);
        if (!$indexer->isScheduled()) {
            $indexer->reindexList($productIds);
        }
    }

}