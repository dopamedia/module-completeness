<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.04.17
 */

namespace Dopamedia\Completeness\Ui\DataProvider\Product;

use Magento\Framework\Data\Collection;
use Magento\Ui\DataProvider\AddFieldToCollectionInterface;

class AddCompletenessFieldToCollection implements AddFieldToCollectionInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * AddCompletenessFieldToCollection constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection|Collection $collection
     * @param string $field
     * @param null $alias
     */
    public function addField(Collection $collection, $field, $alias = null)
    {
        foreach ($this->storeManager->getStores(true) as $store) {
            $collection->joinField(
                sprintf('completeness_%s', $store->getId()),
                'catalog_product_completeness',
                'ratio',
                'entity_id=entity_id',
                '{{table}}.store_id=' . $store->getId(),
                'left'
            );
        }
    }
}