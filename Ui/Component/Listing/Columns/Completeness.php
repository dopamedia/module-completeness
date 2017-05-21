<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.04.17
 */

namespace Dopamedia\Completeness\Ui\Component\Listing\Columns;

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
    )
    {
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
        $storeFilter = $this->context->getFilterParam('store_id');
        $fieldName = $this->getData('name');
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if ($storeFilter) {
                    $item[$fieldName] = $this->decorateStatus($item['completeness_scope']);
                } else {
                    $item[$fieldName] = $this->decorateStatus($item['completeness_default']);
                }
            }
        }

        return $dataSource;

    }


    private function decorateStatus($value)
    {
        if ($value == 0) {
            return "<span class=\"grid-severity-critical\"><span>" . $value . " %</span></span>";
        } elseif ($value < 100) {
            return "<span class=\"grid-severity-minor\"><span>" . $value . " %</span></span>";
        } else {
            return "<span class=\"grid-severity-notice\"><span>" . $value . " %</span></span>";
        }
    }
}