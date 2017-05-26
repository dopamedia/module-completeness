<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 25.05.17
 */

namespace Dopamedia\Completeness\Api\Data;


interface RequirementInterface
{
    /**#@+*/
    const ATTRIBUTE_ID = 'attribute_id';
    const ATTRIBUTE_SET_ID = 'attribute_set_id';
    const STORE_ID = 'store_id';
    const TYPE_ID = 'type_id';
    /**#@-*/

    /**
     * @return int
     */
    public function getAttributeId();

    /**
     * @param int $attributeId
     * @return RequirementInterface
     */
    public function setAttributeId($attributeId);

    /**
     * @return int
     */
    public function getAttributeSetId();

    /**
     * @param int $attributeSetId
     * @return RequirementInterface
     */
    public function setAttributeSetId($attributeSetId);

    /**
     * @return string
     */
    public function getTypeId();

    /**
     * @param string $typeId
     * @return RequirementInterface
     */
    public function setTypeId($typeId);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     * @return RequirementInterface
     */
    public function setStoreId($storeId);

}