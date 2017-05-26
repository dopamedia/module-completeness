<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 25.05.17
 */

namespace Dopamedia\Completeness\Ui\Component\Listing\Columns\Requirement;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Delete extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Delete constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = [
                    'delete' => [
                        'href' => $this->urlBuilder->getUrl(
                            $this->getData('config/deleteUrlPath'),
                            ['requirement_id' => $item['requirement_id']]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete'),
                            'message' => __('Are you sure you wan\'t to delete the record?')
                        ]
                    ]
                ];
            }
        }

        return $dataSource;
    }
}