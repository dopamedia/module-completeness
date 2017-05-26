<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.04.17
 */

namespace Dopamedia\Completeness\Ui\Component\Listing\Columns\Product;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class Completeness extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Completeness constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
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
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $storeNames = [];
        foreach ($this->storeManager->getStores(true) as $store) {
            $storeNames[$store->getId()] = $store->getName();
        }

        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $storeCompleteness = [];
                foreach ($storeNames as $storeId => $storeName) {
                    $storeValueName = sprintf('%s_%s', $fieldName, $storeId);
                    $storeCompleteness[] = sprintf(
                        '%s: %s%%',
                        $storeName,
                        (int)$item[$storeValueName]
                    );
                }
                $item[$fieldName] = implode(', ', $storeCompleteness);
            }
        }

        return $dataSource;
    }
}