<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 13.05.17
 */

namespace Dopamedia\Completeness\Model\ResourceModel\Requirement\Grid;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductTypeList;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection as AttributeSetCollection;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;

class Collection extends AttributeSetCollection implements SearchResultInterface
{

    const TMP_PRODUCT_ENTITY_TYPE_TABLE_NAME = 'catalog_product_entity_type_tmp';

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var \Magento\Catalog\Model\ProductTypes\Config
     */
    private $productTypesConfig;

    /**
     * @var AggregationInterface
     */
    protected $aggregations;

    /**
     * @inheritDoc
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\ProductTypes\Config $productTypesConfig,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->eavConfig = $eavConfig;
        $this->productTypesConfig = $productTypesConfig;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_init(
            'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
            'Magento\Eav\Model\ResourceModel\Entity\Attribute\Set'
        );
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->setEntityTypeFilter($this->getEntityTypeId());

        // select columns
        $this->getSelect()->reset(\Zend_Db_Select::COLUMNS);
        $this->getSelect()->columns(['attribute_set_id']);

        // join store
        $this->join($this->_resource->getTable('store'), null, ['store_id']);

        // join product type
        $this->buildTemporaryProductTypeTable();
        $this->join(self::TMP_PRODUCT_ENTITY_TYPE_TABLE_NAME, null);

        return $this;
    }

    /**
     * @return void
     */
    private function buildTemporaryProductTypeTable()
    {
        $temporaryTable = $this->_resource->getConnection()->newTable(self::TMP_PRODUCT_ENTITY_TYPE_TABLE_NAME);

        $temporaryTable
            ->addColumn('product_type_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false]
            )
            ->addIndex(
                'idx_primary',
                ['product_type_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY]
            );

        $this->_resource->getConnection()->dropTemporaryTable(self::TMP_PRODUCT_ENTITY_TYPE_TABLE_NAME);
        $this->_resource->getConnection()->createTemporaryTable($temporaryTable);

        $productTypeIds = [];
        foreach ($this->productTypesConfig->getAll() as $productTypeConfig) {
            $productTypeIds[] = ['product_type_id' => $productTypeConfig['name']];
        }

        $this->_resource->getConnection()->insertMultiple(
            self::TMP_PRODUCT_ENTITY_TYPE_TABLE_NAME,
            $productTypeIds
        );
    }

    /**
     * @return null|string
     */
    private function getEntityTypeId()
    {
        return $this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)->getEntityTypeId();
    }

    /**
     * @inheritDoc
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * @inheritDoc
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * @inheritDoc
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }
}