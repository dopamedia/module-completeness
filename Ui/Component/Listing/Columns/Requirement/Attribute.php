<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 25.05.17
 */

namespace Dopamedia\Completeness\Ui\Component\Listing\Columns\Requirement;

class Attribute implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    private $collection;

    /**
     * @inheritDoc
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory
    ) {
        $this->collection = $collectionFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {

        $options = [];
        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $item */
        foreach ($this->collection->addVisibleFilter() as $item) {
            $options[] = [
                'value' => $item->getAttributeId(),
                'label' => $item->getDefaultFrontendLabel()
            ];
        }

        return $options;
    }
}