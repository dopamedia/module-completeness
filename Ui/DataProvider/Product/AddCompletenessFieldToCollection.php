<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.04.17
 */

namespace Dopamedia\ProductCompleteness\Ui\DataProvider\Product;

use Magento\Framework\Data\Collection;
use Magento\Ui\DataProvider\AddFieldToCollectionInterface;

class AddCompletenessFieldToCollection implements AddFieldToCollectionInterface
{
    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection|Collection $collection
     * @param string $field
     * @param null $alias
     */
    public function addField(Collection $collection, $field, $alias = null)
    {
        // TODO::add store_id as condition
        $collection->joinField(
            'completeness',
            'catalog_product_completeness',
            'ratio',
            'entity_id=entity_id',
            '{{table}}.store_id=' . $collection->getStoreId(),
            'left'
        );
    }
}