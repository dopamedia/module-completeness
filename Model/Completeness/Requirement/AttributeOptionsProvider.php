<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 20.05.17
 */

namespace Dopamedia\Completeness\Model\Completeness\Requirement;

use Magento\Backend\App\Area\FrontNameResolver;

class AttributeOptionsProvider implements \Magento\Framework\Data\OptionSourceInterface
{

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    private $attributeCollection;

    /**
     * AttributeOptionsProvider constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $attributeCollection
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $attributeCollection
    ) {
        $this->request = $request;
        $this->attributeCollection = $attributeCollection;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {

        $attributeSetId = $this->request->getParam('attribute_set_id');
        $productTypeId = $this->request->getParam('product_type_id');
        $storeId = $this->request->getParam('store_id');

        $this->attributeCollection->setAttributeSetFilter($attributeSetId);

        if ($storeId != \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
            $this->attributeCollection->addFieldToFilter(
                'is_global',
                \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE
            );
        }

        $result = [];

        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        foreach ($this->attributeCollection as $attribute) {

            if (!empty($attribute->getApplyTo()) && !in_array($productTypeId, $attribute->getApplyTo())) {
                continue;
            }

            $result[] = [
                'value' => $attribute->getId(),
                'label' => $attribute->getDefaultFrontendLabel()
            ];
        }

        return $result;
    }

}