<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.04.17
 */

namespace Dopamedia\Completeness\Model\Indexer\Completeness\Product;

use Magento\Framework\App\ResourceConnection;

abstract class AbstractAction
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Dopamedia\Completeness\Helper\IndexerFactory
     */
    protected $indexerHelperFactory;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * AbstractAction constructor.
     * @param ResourceConnection $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Dopamedia\Completeness\Helper\IndexerFactory $indexerHelperFactory
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     */
    public function __construct(
        ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Dopamedia\Completeness\Helper\IndexerFactory $indexerHelperFactory,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->storeManager = $storeManager;
        $this->indexerHelperFactory = $indexerHelperFactory;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @return AbstractAction
     */
    abstract public function execute();

    /**
     * @param int $storeId
     * @param array|null $changedIds
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function reindexByStore(int $storeId, array $changedIds = null)
    {
        try {
            $this->buildTemporaryTable($storeId, $changedIds);
            $this->updateCompleteness($storeId, $changedIds);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    private function getTemporaryTableName(int $storeId): string
    {
        return sprintf('catalog_product_completeness_tmp_indexer_%s', $storeId);
    }

    /**
     * @param int $storeId
     * @param array|null $changedIds
     * @return void
     */
    private function buildTemporaryTable(int $storeId, array $changedIds = null)
    {
        $attributes = $this->indexerHelperFactory->create()->getAttributes($storeId);
        $this->createTemporaryTable($storeId);
        $this->fillTemporaryTable($storeId, $attributes, $changedIds);
    }

    /**
     * @param int $storeId
     * @return void
     */
    private function createTemporaryTable(int $storeId)
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

        $this->connection->dropTable($temporaryName);
        $this->connection->createTable($temporaryTable);
    }

    /**
     * @param int $storeId
     * @param array $attributes
     * @param array|null $changedIds
     * @return void
     */
    private function fillTemporaryTable(int $storeId, array $attributes, array $changedIds = null)
    {
        $selects = [];
        $productMetadata = $this->metadataPool->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class);

        // TODO::find better way to avoid SQLSTATE[42000] if there are not attributes
        if (empty($attributes)) {
            return;
        }

        foreach ($attributes as $attribute) {

            $unionSelect = $this->connection->select()->from(
                ['e' => $this->resource->getTableName('catalog_product_entity')],
                [$productMetadata->getLinkField()]
            );

            if ($changedIds !== null) {
                $unionSelect->where(
                    sprintf('e.%s IN (?)', $productMetadata->getLinkField()),
                    $changedIds
                );
            }

            $unionSelect->columns(['attribute_id' => new \Zend_Db_Expr($attribute->getId())]);

            $joinCondition = sprintf(
                'e.%3$s = %1$s.%3$s AND %1$s.attribute_id = %2$d AND %1$s.store_id = %4$d',
                'value_table',
                $attribute->getId(),
                $productMetadata->getLinkField(),
                $storeId
            );

            $unionSelect->joinLeft(
                ['value_table' => $attribute->getBackendTable()],
                $joinCondition,
                ['filled' => new \Zend_Db_Expr(sprintf('%s.value IS NOT NULL', 'value_table'))]
            );

            $selects[] = $unionSelect;
        }

        /** @var \Magento\Framework\Db\Select $select */
        $select = $this->connection->select()->union($selects, \Magento\Framework\DB\Select::SQL_UNION_ALL);

        $sql = $select->insertFromSelect($this->getTemporaryTableName($storeId));
        $this->connection->query($sql);
    }

    /**
     * @param int $storeId
     * @param array|null $changedIds
     */
    private function updateCompleteness(int $storeId, array $changedIds = null)
    {
        $connection = $this->connection;

        $requiredAttributeIdsSelect = $connection->select()
            ->from(
                ['cpcr' => $connection->getTableName('catalog_product_completeness_requirement')],
                ['attribute_id']
            )
            ->where('cpcr.type_id = cpe.type_id')
            ->where('cpcr.attribute_set_id = cpe.attribute_set_id')
            ->where('cpcr.store_id = (?)', $storeId);

        $requiredValuesFilledSelect = $connection->select()
            ->from(
                ['cpcti' => $this->getTemporaryTableName($storeId)],
                ['filled' => 'SUM(filled)']
            )
            ->where('cpcti.entity_id = cpe.entity_id')
            ->where('cpcti.attribute_id IN ?', $requiredAttributeIdsSelect)
            ->group('cpe.entity_id');

        $valuesFilledSelect = $connection->select()
            ->from(
                ['cpe' => $connection->getTableName('catalog_product_entity')],
                [
                    'cpe.entity_id',
                    'cpe.type_id',
                    'cpe.attribute_set_id',
                    'req_values_filled' => $requiredValuesFilledSelect
                ]
            )->group('cpe.entity_id');


        if ($changedIds !== null) {
            $valuesFilledSelect->where('cpe.entity_id IN (?)', $changedIds);
        }

        $requiredCountSelect = $connection->select()
            ->from(
                ['cpcr' => $connection->getTableName('catalog_product_completeness_requirement')],
                ['required_count' => new \Zend_Db_Expr('COUNT(*)')]
            )
            ->where('cpcr.type_id = values_filled.type_id')
            ->where('cpcr.attribute_set_id = values_filled.attribute_set_id')
            ->where('cpcr.store_id = ?', $storeId);

        $resultSelect = $connection->select()
            ->from(
                ['values_filled' => $valuesFilledSelect],
                [
                    'values_filled.entity_id',
                    'values_filled.req_values_filled',
                    'required_count' => $requiredCountSelect
                ]
            );

        $select = $connection->select()
            ->from(
                ['result' => $resultSelect],
                [
                    'entity_id',
                    'store_id' => new \Zend_Db_Expr($storeId),
                    'missing_count' => 'IFNULL((required_count - req_values_filled), 0)',
                    'required_count',
                    'ratio' => 'IFNULL((req_values_filled / required_count * 100), 0)'
                ]
            );

        $insertQuery = $connection->insertFromSelect(
            $select,
            $this->connection->getTableName('catalog_product_completeness'),
            [],
            \Magento\Framework\DB\Adapter\Pdo\Mysql::INSERT_ON_DUPLICATE
        );

        $connection->query($insertQuery);
    }
}