<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.04.17
 */

namespace Dopamedia\Completeness\Plugin;

use Magento\Catalog\Model\ResourceModel\Product as ResourceProduct;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Product
 * @package Dopamedia\Completeness\Plugin
 */
class Product extends AbstractPlugin
{
    /**
     * Reindex on product save
     *
     * @param ResourceProduct $productResource
     * @param \Closure $proceed
     * @param AbstractModel $product
     * @return ResourceProduct
     */
    public function aroundSave(ResourceProduct $productResource, \Closure $proceed, AbstractModel $product): ResourceProduct
    {
        return $this->addCommitCallback($productResource, $proceed, $product);
    }

    /**
     * Reindex on product delete
     *
     * @param ResourceProduct $productResource
     * @param \Closure $proceed
     * @param AbstractModel $product
     * @return ResourceProduct
     */
    public function aroundDelete(ResourceProduct $productResource, \Closure $proceed, AbstractModel $product): ResourceProduct
    {
        return $this->addCommitCallback($productResource, $proceed, $product);
    }

    /**
     * @param ResourceProduct $productResource
     * @param \Closure $proceed
     * @param AbstractModel $product
     * @return ResourceProduct
     * @throws \Exception
     */
    private function addCommitCallback(ResourceProduct $productResource, \Closure $proceed, AbstractModel $product): ResourceProduct
    {
        try {
            $productResource->beginTransaction();
            $result = $proceed($product);
            $productResource->addCommitCallback(function () use ($product) {
                $this->reindexRow($product->getEntityId());
            });
            $productResource->commit();
        } catch (\Exception $e) {
            $productResource->rollBack();
            throw $e;
        }

        return $result;
    }
}