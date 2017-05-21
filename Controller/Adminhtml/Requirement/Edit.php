<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 16.05.17
 */

namespace Dopamedia\Completeness\Controller\Adminhtml\Requirement;

use Dopamedia\Completeness\Controller\Adminhtml\Requirement;

class Edit extends Requirement
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Dopamedia_Completeness::completeness_requirement');
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Completeness Requirement'));

        return $resultPage;
    }

}