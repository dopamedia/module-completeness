<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.04.17
 */

namespace Dopamedia\ProductCompleteness\Model\Indexer\Completeness;

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
     * @var \Dopamedia\ProductCompleteness\Helper\Indexer
     */
    protected $indexerHelper;

    /**
     * @var TableBuilder
     */
    private $tableBuilder;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * AbstractAction constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Dopamedia\ProductCompleteness\Helper\Indexer $indexerHelper,
        \Dopamedia\ProductCompleteness\Model\Indexer\Completeness\TableBuilder $tableBuilder,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->storeManager = $storeManager;
        $this->indexerHelper = $indexerHelper;
        $this->tableBuilder = $tableBuilder;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @return AbstractAction
     */
    abstract public function execute(): AbstractAction;

    /**
     * @param int $storeId
     * @param array $changedIds
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function reindexByStore(int $storeId, array $changedIds = [])
    {
        try {
            $this->tableBuilder->build($storeId, $changedIds);
            $this->updateCompleteness($storeId, $changedIds);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
    }

    /**
     * TODO::replace raw query with DDL
     *
     * @param int $storeId
     * @param array $changedIds
     */
    private function updateCompleteness(int $storeId, array $changedIds = [])
    {
        $bind = [
            'storeId' => $storeId
        ];

        $rawQuery = <<<SQL
INSERT INTO catalog_product_completeness
  SELECT
    entity_id,
    :storeId                                   AS store_id,
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
                   AND cpcr.store_id = :storeId
           ) AS required_count
         FROM (
                SELECT
                  cpe.entity_id,
                  cpe.type_id,
                  cpe.attribute_set_id,
                  (
                    SELECT SUM(filled) AS filled
                    FROM %s AS cpcti
                    WHERE cpcti.entity_id = cpe.entity_id
                          AND cpcti.attribute_id IN (
                      SELECT attribute_id
                      FROM catalog_product_completeness_requirement AS cpcr
                      WHERE cpcr.type_id = cpe.type_id
                            AND cpcr.attribute_set_id = cpe.attribute_set_id
                            AND cpcr.store_id = :storeId
                    )
                    GROUP BY cpe.entity_id
                  ) AS req_values_filled
                FROM catalog_product_entity AS cpe
                GROUP BY cpe.entity_id
              ) AS values_filled
       ) AS result
ON DUPLICATE KEY UPDATE
  missing_count  = VALUES(missing_count),
  required_count = VALUES(required_count),
  ratio          = VALUES(ratio);
SQL;

        $query = $this->connection->query(sprintf($rawQuery, $this->tableBuilder->getTemporaryTableName($storeId)), $bind);
        $query->execute();
    }
}