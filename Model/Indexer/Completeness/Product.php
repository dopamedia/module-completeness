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
     * @var Product\Action\FullFactory
     */
    protected $fullActionFactory;

    /**
     * @var Product\Action\RowsFactory
     */
    protected $rowsActionFactory;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * Product constructor.
     * @param Product\Action\FullFactory $fullActionFactory
     * @param Product\Action\RowsFactory $rowsActionFactory
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     */
    public function __construct(
        \Dopamedia\Completeness\Model\Indexer\Completeness\Product\Action\FullFactory $fullActionFactory,
        \Dopamedia\Completeness\Model\Indexer\Completeness\Product\Action\RowsFactory $rowsActionFactory,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
    ) {
        $this->fullActionFactory = $fullActionFactory;
        $this->indexerRegistry = $indexerRegistry;
        $this->rowsActionFactory = $rowsActionFactory;
    }

    /**
     * @inheritDoc
     */
    public function executeFull()
    {
        $this->fullActionFactory->create()->execute();
    }

    /**
     * @inheritDoc
     */
    public function executeList(array $ids)
    {
        $this->executeAction($ids);

    }

    /**
     * @inheritDoc
     */
    public function executeRow($id)
    {
        $this->executeAction([$id]);
    }

    /**
     * @inheritDoc
     */
    public function execute($ids)
    {
        $this->executeAction($ids);
    }

    /**
     * @param int[] $ids
     * @return Product
     */
    protected function executeAction(array $ids): Product
    {
        $ids = array_unique($ids);
        $indexer = $this->indexerRegistry->get(static::INDEXER_ID);

        /** @var Product\Action\Rows $action */
        $action = $this->rowsActionFactory->create();
        if ($indexer->isWorking()) {
            $action->execute($ids, true);
        }
        $action->execute($ids);

        return $this;
    }

}