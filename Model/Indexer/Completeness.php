<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.04.17
 */

namespace Dopamedia\ProductCompleteness\Model\Indexer;


class Completeness implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'catalog_product_completeness';

    /**
     * @var Completeness\Action\FullFactory
     */
    protected $fullActionFactory;

    /**
     * @var Completeness\Action\RowsFactory
     */
    protected $rowsActionFactory;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * Completeness constructor.
     * @param Completeness\Action\FullFactory $fullActionFactory
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     */
    public function __construct(
        \Dopamedia\ProductCompleteness\Model\Indexer\Completeness\Action\FullFactory $fullActionFactory,
        \Dopamedia\ProductCompleteness\Model\Indexer\Completeness\Action\RowsFactory $rowsActionFactory,
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
     * @return Completeness
     */
    protected function executeAction(array $ids): Completeness
    {
        $ids = array_unique($ids);
        $indexer = $this->indexerRegistry->get(static::INDEXER_ID);

        /** @var Completeness\Action\Rows $action */
        $action = $this->rowsActionFactory->create();
        if ($indexer->isWorking()) {
            $action->execute($ids, true);
        }
        $action->execute($ids);

        return $this;
    }

}