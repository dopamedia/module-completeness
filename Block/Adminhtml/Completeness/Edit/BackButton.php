<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 20.05.17
 */

namespace Dopamedia\Completeness\Block\Adminhtml\Completeness\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * @inheritdoc
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }

}