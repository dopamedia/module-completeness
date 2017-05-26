<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.04.17
 */

namespace Dopamedia\Completeness\Ui\DataProvider\Product;

use Magento\Framework\Data\Collection;
use Magento\TestFramework\Event\Magento;
use Magento\Ui\DataProvider\AddFieldToCollectionInterface;

class AddCompletenessFieldToCollection implements AddFieldToCollectionInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;
    /**
     * @var \Magento\Framework\View\Element\UiComponent
     */
    private $uiComponent;
    /**
     * @var \Magento\Framework\View\Element\UiComponent\Context
     */
    private $context;

    /**
     * AddCompletenessFieldToCollection constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Framework\App\Request\Http $request,
                                \Magento\Framework\View\Element\UiComponent\Context $context)
    {
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->context = $context;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection|Collection $collection
     * @param string $field
     * @param null $alias
     */
    public function addField(Collection $collection, $field, $alias = null)
    {
        $storeFilter = $this->context->getFilterParam('store_id');
        if ($storeFilter) {
            $storeId = $collection->getStoreId();
            $collection->joinField(
                'completeness_scope',
                'catalog_product_completeness',
                'ratio',
                'entity_id=entity_id',
                '{{table}}.store_id=' . $storeId,
                'left'
            );
        } else {
            $collection->joinField(
                'completeness_default',
                'catalog_product_completeness',
                'ratio',
                'entity_id=entity_id',
                '{{table}}.store_id=' . \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                'left'
            );
        }

    }
}