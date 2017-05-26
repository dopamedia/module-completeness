<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.05.17
 */

namespace Dopamedia\Completeness\Model;

use Dopamedia\Completeness\Api\Data\RequirementInterface;

class Requirement extends \Magento\Framework\Model\AbstractModel implements RequirementInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('Dopamedia\Completeness\Model\ResourceModel\Requirement');
    }

    /**
     * @inheritDoc
     */
    public function getAttributeId()
    {
        return $this->getData(self::ATTRIBUTE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setAttributeId($attributeId)
    {
        return $this->setData(self::ATTRIBUTE_ID, $attributeId);
    }

    /**
     * @inheritDoc
     */
    public function getAttributeSetId()
    {
        return $this->getData(self::ATTRIBUTE_SET_ID);
    }

    /**
     * @inheritDoc
     */
    public function setAttributeSetId($attributeSetId)
    {
        return $this->setData(self::ATTRIBUTE_SET_ID, $attributeSetId);
    }

    /**
     * @inheritDoc
     */
    public function getTypeId()
    {
        return $this->getData(self::TYPE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setTypeId($typeId)
    {
        return $this->setData(self::TYPE_ID, $typeId);
    }

    /**
     * @inheritDoc
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }
}