<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 26.05.17
 */

namespace Dopamedia\Completeness\Block\Adminhtml\Requirement\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class Add
 * @package Dopamedia\Completeness\Block\Adminhtml\Requirement\Button
 */
class Add implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Add Requirement'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ]
        ];
    }
}