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

        $this->connection->dropTemporaryTable($temporaryName);
        $this->connection->createTemporaryTable($temporaryTable);
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


        echo $select->__toString();
        die();

        $sql = $select->insertFromSelect($this->getTemporaryTableName($storeId));
        $this->connection->query($sql);
    }

    /**
     * TODO::replace raw query with DDL
     *
     * @param int $storeId
     * @param array|null $changedIds
     */
    private function updateCompleteness(int $storeId, array $changedIds = null)
    {
        $rawQuery = <<<SQL
INSERT INTO catalog_product_completeness
  SELECT
    entity_id,
    %store_id%                                 AS store_id,
    (required_count - req_values_filled)       AS missing_count,
    required_count,
    (req_values_filled / required_count * 100) AS ratio
  FROM (
         SELECT
           values_filled.entity_id,
           values_filled.req_values_filled,
           (
             SELECT COUNT(*)
             FROM catalog_product_completeness_requirement AS cpcr
             WHERE cpcr.type_id = values_filled.type_id
                   AND cpcr.attribute_set_id = values_filled.attribute_set_id
                   AND cpcr.store_id = %store_id%
           ) AS required_count
         FROM (
                SELECT
                  cpe.entity_id,
                  cpe.type_id,
                  cpe.attribute_set_id,
                  (
                    SELECT SUM(filled) AS filled
                    FROM %temporary_table_name% AS cpcti
                    WHERE cpcti.entity_id = cpe.entity_id
                          AND cpcti.attribute_id IN (
                      SELECT attribute_id
                      FROM catalog_product_completeness_requirement AS cpcr
                      WHERE cpcr.type_id = cpe.type_id
                            AND cpcr.attribute_set_id = cpe.attribute_set_id
                            AND cpcr.store_id = %store_id%
                    )
                    GROUP BY cpe.entity_id
                  ) AS req_values_filled
                FROM catalog_product_entity AS cpe
                %entity_id_limitation%
                GROUP BY cpe.entity_id
              ) AS values_filled
       ) AS result
ON DUPLICATE KEY UPDATE
  missing_count  = VALUES(missing_count),
  required_count = VALUES(required_count),
  ratio          = VALUES(ratio);
SQL;

        $entityIdLimitation = ($changedIds === null)
            ? ''
            : sprintf('WHERE cpe.entity_id IN (%s)', implode(',', $changedIds));

        $rawQuery = strtr($rawQuery, [
            '%temporary_table_name%' => $this->getTemporaryTableName($storeId),
            '%store_id%' => $storeId,
            '%entity_id_limitation%' => $entityIdLimitation
        ]);


        $query = $this->connection->query($rawQuery);
        $query->execute();
    }
}