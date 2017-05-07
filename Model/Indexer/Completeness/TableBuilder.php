<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 05.05.17
 */

namespace Dopamedia\ProductCompleteness\Model\Indexer\Completeness;

class TableBuilder
{
    /**
     * @var \Dopamedia\ProductCompleteness\Helper\Indexer
     */
    private $indexerHelper;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

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
     * @param \Dopamedia\ProductCompleteness\Helper\Indexer $indexerHelper
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     */
    public function __construct(
        \Dopamedia\ProductCompleteness\Helper\Indexer $indexerHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool
    )
    {
        $this->indexerHelper = $indexerHelper;
        $this->resource = $resource;
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
        $tableName = $this->getTemporaryTableName($storeId);

        $connection = $this->resource->getConnection();

        // TODO::replace with newTemporaryTable, dropTemporaryTable and createTemporaryTable
        $table = $connection->newTable($tableName);
        $table->addColumn('entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER);
        $table->addColumn('attribute_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER);
        $table->addColumn('filled', \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN);
        $connection->dropTable($tableName);
        $connection->createTable($table);
    }

    /**
     * @param int $storeId
     * @param \Magento\Eav\Model\Entity\Attribute[] $attributes
     * @return void
     */
    private function fillTemporaryTable(int $storeId, array $attributes)
    {
        $connection = $this->resource->getConnection();
        $select = $connection->select();
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
            $connection->query($sql);
        }
    }
}