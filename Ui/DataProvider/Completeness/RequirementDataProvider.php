<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 20.05.17
 */

namespace Dopamedia\Completeness\Ui\DataProvider\Completeness;

/**
 * Class RequirementDataProvider
 * @package Dopamedia\Completeness\Ui\DataProvider\Completeness
 */
class RequirementDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    const PARAM_KEY_ATTRIBUTE_SET_ID = 'attribute_set_id';
    const PARAM_KEY_PRODUCT_TYPE_ID = 'product_type_id';
    const PARAM_KEY_STORE_ID = 'store_id';

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Eav\Api\AttributeSetRepositoryInterface
     */
    private $attributeSetRepository;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\GroupFactory
     */
    private $attributeGroupFactory;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * @inheritDoc
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRepository,
        \Magento\Eav\Model\Entity\Attribute\GroupFactory $attributeGroupFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->request = $request;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->attributeGroupFactory = $attributeGroupFactory;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @inheritDoc
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        return;
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getConfigData()
    {
        $config = parent::getConfigData();

        $config['data'] = [
            'attribute_set_id' => $this->request->getParam('attribute_set_id'),
            'product_type_id' => $this->request->getParam('product_type_id'),
            'store_id' => $this->request->getParam('store_id')
        ];

        return $config;
    }

    /**
     * @return string
     */
    private function getAttributeSetId()
    {
        return $this->request->getParam(self::PARAM_KEY_ATTRIBUTE_SET_ID);
    }

    /**
     * @return string
     */
    private function getProductTypeId()
    {
        return $this->request->getParam(self::PARAM_KEY_PRODUCT_TYPE_ID);
    }

    /**
     * @return mixed
     */
    private function getStoreId()
    {
        return $this->request->getParam(self::PARAM_KEY_STORE_ID);
    }

    /**
     * @return \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\Collection
     */
    protected function getAttributeGroups()
    {
        return $this->attributeGroupFactory->create()
            ->getResourceCollection()
            ->setAttributeSetFilter($this->getAttributeSetId())
            ->setSortOrder()
            ->load();
    }


    /**
     * @param \Magento\Eav\Model\Entity\Attribute\Group $attributeGroup
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    private function getAttributes(\Magento\Eav\Model\Entity\Attribute\Group $attributeGroup)
    {
        $attributes = $this->attributeCollectionFactory->create()
            ->setAttributeGroupFilter($attributeGroup->getId())
            ->setAttributeSetFilter($this->getAttributeSetId())
            ->addVisibleFilter();

        if ($this->getStoreId() != \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
            $attributes->addFieldToFilter(
                'additional_table.is_global',
                ['in' => [\Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE]]
            );
        }

        return $attributes;
    }

    /**
     * @inheritDoc
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        return $this->prepareMeta($meta);
    }

    /**
     * Prepare meta data
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta($meta)
    {
        /** @var \Magento\Eav\Model\Entity\Attribute\Group $attributeGroup */
        foreach ($this->getAttributeGroups() as $attributeGroup) {
            $attributes = $this->getAttributes($attributeGroup);

            $fieldset = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => $attributeGroup->getAttributeGroupName(),
                            'componentType' => 'fieldset',
                            'collapsible' => true
                        ]
                    ]
                ],
                'children' => []
            ];

            /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
            foreach ($attributes as $attribute) {

                if (!empty($attribute->getApplyTo()) && !in_array($this->getProductTypeId(), $attribute->getApplyTo())) {
                    continue;
                }

                $fieldset['children'][$attribute->getAttributeCode()] = [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'dataType' => 'boolean',
                                'label' => $attribute->getDefaultFrontendLabel(),
                                'componentType' => 'checkbox',
                                'prefer' => 'toggle',
                                'source' => 'requirement',
                                'dataScope' => $attribute->getAttributeCode(),
                                'valueMap' => [
                                    'true' => 1,
                                    'false' => 0
                                ],
                                'default' => 0
                            ]
                        ]
                    ]
                ];
            }

            if (!empty($fieldset['children'])) {
                $meta[$attributeGroup->getAttributeGroupCode()] = $fieldset;
            }
        }

        return $meta;
    }
}