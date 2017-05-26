<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 25.05.17
 */

namespace Dopamedia\Completeness\Controller\Adminhtml\Requirement;

use Dopamedia\Completeness\Controller\Adminhtml\Requirement;
use Dopamedia\Completeness\Model\Repository;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Delete extends Requirement
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @inheritDoc
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Repository $repository
    ) {
        $this->repository = $repository;
        parent::__construct($context, $resultPageFactory);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $id = $this->getRequest()->getParam('requirement_id');
        if ($id) {
            try {
                $this->repository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the requirement'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a requirement to delete.'));
        return $resultRedirect->setPath('*/*/');
    }

}