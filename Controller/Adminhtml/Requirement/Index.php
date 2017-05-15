<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 13.05.17
 */

namespace Dopamedia\Completeness\Controller\Adminhtml\Requirement;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package Dopamedia\Completeness\Controller\Adminhtml\Requirement
 */
class Index extends Action
{
    const ADMIN_RESOURCE = 'Dopamedia_Completeness::completeness_requirement';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Dopamedia_Completeness::completeness_requirement');
        $resultPage->getConfig()->getTitle()->prepend(__('Completeness Requirement'));

        return $resultPage;
    }
}