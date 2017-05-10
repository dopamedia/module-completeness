<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.04.17
 */

namespace Dopamedia\Completeness\Model\Indexer\Completeness;

class Product implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'catalog_product_completeness';

    /**
     * @var Product\Action\Full
     */
    protected $fullAction;

    /**
     * @var Product\Action\Rows
     */
    protected $rowsAction;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * Product constructor.
     * @param Product\Action\Full $fullAction
     * @param Product\Action\Rows $rowsAction
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     */
    public function __construct(
        \Dopamedia\Completeness\Model\Indexer\Completeness\Product\Action\Full $fullAction,
        \Dopamedia\Completeness\Model\Indexer\Completeness\Product\Action\Rows $rowsAction,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
    ) {
        $this->fullAction = $fullAction;
        $this->rowsAction = $rowsAction;
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * @inheritDoc
     */
    public function executeFull()
    {
        $this->fullAction->execute();
    }

    /**
     * @inheritDoc
     */
    public function executeList(array $ids)
    {
        $this->rowsAction->execute($ids);
    }

    /**
     * @inheritDoc
     */
    public function executeRow($id)
    {
        $this->rowsAction->execute([$id]);
    }

    /**
     * @inheritDoc
     */
    public function execute($ids)
    {
        $this->rowsAction->execute($ids);
    }
}