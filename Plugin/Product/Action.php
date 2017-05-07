<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.04.17
 */

namespace Dopamedia\ProductCompleteness\Plugin\Product;

use Dopamedia\ProductCompleteness\Plugin\AbstractPlugin;

/**
 * Class Action
 * @package Dopamedia\ProductCompleteness\Plugin\Product
 */
class Action extends AbstractPlugin
{
    /**
     * Reindex on product attribute mass change
     *
     * @param \Magento\Catalog\Model\Product\Action $subject
     * @param \Closure $closure
     * @param array $productIds
     * @param array $attrData
     * @param int $storeId
     * @return \Magento\Catalog\Model\Product\Action
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundUpdateAttributes(
        \Magento\Catalog\Model\Product\Action $subject,
        \Closure $closure,
        array $productIds,
        array $attrData,
        int $storeId
    ): \Magento\Catalog\Model\Product\Action {

        $result = $closure($productIds, $attrData, $storeId);
        $this->reindexList(array_unique($productIds));
        return $result;
    }
}