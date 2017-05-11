<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.04.17
 */

namespace Dopamedia\Completeness\Helper;

class Indexer extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var null|array
     */
    private $attributesBuffer;

    /**
     * @var null|array
     */
    private $attributeCodesBuffer;
    
    /**
     * Indexer constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->resource = $resource;
        $this->eavConfig = $eavConfig;
        parent::__construct($context);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getTable(string $name): string
    {
        return $this->resource->getTableName($name);
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getAttributes(int $storeId): array
    {
        if ($this->attributesBuffer === null) {
            $this->attributesBuffer = [];
            $attributeCodes = $this->getAttributeCodes($storeId);
            /** @var \Magento\Eav\Model\Entity\AbstractEntity $entity */
            $entity = $this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)->getEntity();

            foreach ($attributeCodes as $attributeCode) {
                $attribute = $this->eavConfig->getAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $attributeCode
                )->setEntity(
                    $entity
                );
                try {
                    // check if exists source and backend model.
                    // To prevent exception when some module was disabled
                    $attribute->usesSource() && $attribute->getSource();
                    $attribute->getBackend();
                    $this->attributesBuffer[$attributeCode] = $attribute;
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                }
            }
        }
        return $this->attributesBuffer;
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getAttributeCodes(int $storeId): array
    {
        if ($this->attributeCodesBuffer === null) {
            $connection = $this->resource->getConnection();
            $this->attributeCodesBuffer = [];

            $select = $connection->select()->from(
                ['requirement_table' => $this->getTable('catalog_product_completeness_requirement')]
            )->join(
                $this->getTable('eav_attribute'),
                'requirement_table.attribute_id = eav_attribute.attribute_id'
            )->where(
                'requirement_table.store_id = :store_id'
            );

            $attributesData = $connection->fetchAll($select, ['store_id' => $storeId]);

            foreach ($attributesData as $data) {
                $this->attributeCodesBuffer[$data['attribute_id']] = $data['attribute_code'];
            }

        }
        return $this->attributeCodesBuffer;
    }

}