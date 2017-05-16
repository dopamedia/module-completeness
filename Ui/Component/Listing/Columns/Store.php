<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 16.05.17
 */

namespace Dopamedia\Completeness\Ui\Component\Listing\Columns;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Store extends Column implements OptionSourceInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var null|array
     */
    protected $options;

    /**
     * @inheritDoc
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            /** @var \Magento\Store\Model\Store $store */
            foreach ($this->storeManager->getStores(true) as $store) {
                $this->options[] = ['value' => $store->getId(), 'label' => $store->getName()];
            }
        }

        return $this->options;
    }
}