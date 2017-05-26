<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 26.05.17
 */

namespace Dopamedia\Completeness\Ui\DataProvider\Requirement\Listing;


use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\Component;
use Magento\Framework\UrlInterface;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @inheritDoc
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        UrlInterface $urlBuilder,
        array $meta = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data);
    }

    /**
     * @inheritDoc
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        return $this->modifyMeta($meta);
    }

    /**
     * @param array $meta
     * @return array
     */
    private function modifyMeta(array $meta)
    {
        $meta['modalContainer']['children']['add_requirement_modal'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'isTemplate' => false,
                        'componentType' => Component\Modal::NAME,
                        'options' => [
                            'title' => __('Add Requirement')
                        ],
                        'imports' => [
                            'state' => '!index=add_requirement_form:responseStatus'
                        ],
                    ]
                ]
            ],
            'children' => [
                'add_requirement_form' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Add Requirement'),
                                'componentType' => Component\Container::NAME,
                                'component' => 'Magento_Ui/js/form/components/insert-form',
                                'dataScope' => '',
                                'update_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'render_url' => $this->urlBuilder->getUrl(
                                    'mui/index/render_handle',
                                    [
                                        'handle' => 'completeness_requirement_add',
                                        'buttons' => 1
                                    ]
                                ),
                                'autoRender' => false,
                                'ns' => 'completeness_requirement_form',
                                'externalProvider' => 'completeness_requirement_form.requirement_form_data_source',
                                'toolbarContainer' => '${ $.parentName }',
                                'formSubmitType' => 'ajax'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $meta;
    }
}