<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 16.05.17
 */

namespace Dopamedia\Completeness\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

abstract class Requirement extends Action
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
}