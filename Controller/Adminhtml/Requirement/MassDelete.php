<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 25.05.17
 */

namespace Dopamedia\Completeness\Controller\Adminhtml\Requirement;

use Dopamedia\Completeness\Controller\Adminhtml\Requirement;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Dopamedia\Completeness\Model\ResourceModel\Requirement\CollectionFactory;
use Dopamedia\Completeness\Model\Repository;

class MassDelete extends Requirement
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @inheritDoc
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        PageFactory $resultPageFactory,
        Repository $repository
    )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->repository = $repository;
        parent::__construct($context, $resultPageFactory);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        /** @var \Dopamedia\Completeness\Model\Requirement $requirement */
        foreach ($collection as $requirement) {
            $this->repository->delete($requirement);
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}