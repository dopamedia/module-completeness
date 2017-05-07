<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 05.05.17
 */

namespace Dopamedia\Completeness\Model\Indexer\Completeness\Product;

class TableBuilder
{
    /**
     * @var \Dopamedia\Completeness\Helper\Indexer
     */
    private $indexerHelper;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * @var bool
     */
    protected $isExecuted = false;

    /**
     * TableBuilder constructor.
     * @param \Dopamedia\Completeness\Helper\Indexer $indexerHelper
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     */
    public function __construct(
        \Dopamedia\Completeness\Helper\Indexer $indexerHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool
    ) {
        $this->indexerHelper = $indexerHelper;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->metadataPool = $metadataPool;
    }

    /**
     * @param int $storeId
     * @param array $changedIds
     * @return void
     */
    public function build(int $storeId, array $changedIds = [])
    {
        if ($this->isExecuted) {
            return;
        }

        $attributes = $this->indexerHelper->clearAttributesBuffer()->getAttributes($storeId);
        $this->createTemporaryTable($storeId, $attributes);
        $this->fillTemporaryTable($storeId, $attributes);
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getTemporaryTableName(int $storeId): string
    {
        return sprintf('catalog_product_completeness_tmp_indexer_%s', $storeId);
    }

    /**
     * @param int $storeId
     * @param \Magento\Eav\Model\Entity\Attribute[] $attributes
     * @return void
     */
    private function createTemporaryTable(int $storeId, array $attributes)
    {
        $temporaryName = $this->getTemporaryTableName($storeId);
        $temporaryTable = $this->connection->newTable($temporaryName);

        $temporaryTable
            ->addColumn('entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER)
            ->addColumn('attribute_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER)
            ->addColumn('filled', \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN)
            ->addIndex(
                'idx_primary',
                    ['entity_id', 'attribute_id'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY]
            );

        $this->connection->dropTemporaryTable($temporaryName);
        $this->connection->createTemporaryTable($temporaryTable);
    }

    /**
     * @param int $storeId
     * @param \Magento\Eav\Model\Entity\Attribute[] $attributes
     * @return void
     */
    private function fillTemporaryTable(int $storeId, array $attributes)
    {
        $select = $this->connection->select();
        $productMetadata = $this->metadataPool->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class);

        // TODO::find better way to save data to tmp table (maybe this could be done with a single query)
        foreach ($attributes as $attribute) {
            $select->reset()->from(
                ['e' => $this->resource->getTableName('catalog_product_entity')],
                [$productMetadata->getLinkField()]
            );

            $select->columns(['attribute_id' => new \Zend_Db_Expr($attribute->getId())]);

            $joinCondition = sprintf(
                'e.%3$s = %1$s.%3$s AND %1$s.attribute_id = %2$d AND %1$s.store_id = %4$d',
                'attribute_table',
                $attribute->getId(),
                $productMetadata->getLinkField(),
                $storeId
            );

            $select->joinLeft(
                ['attribute_table' => $attribute->getBackendTable()],
                $joinCondition,
                ['filled' => new \Zend_Db_Expr(sprintf('%s.value IS NOT NULL', 'attribute_table'))]
            );

            $sql = $select->insertFromSelect($this->getTemporaryTableName($storeId));
            $this->connection->query($sql);
        }
    }
}