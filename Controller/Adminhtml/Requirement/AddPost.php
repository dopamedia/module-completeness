<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 26.05.17
 */

namespace Dopamedia\Completeness\Controller\Adminhtml\Requirement;

use Dopamedia\Completeness\Controller\Adminhtml\Requirement;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class AddPost extends Requirement
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    private $layoutFactory;
    /**
     * @var \Dopamedia\Completeness\Api\Data\RequirementInterfaceFactory
     */
    private $requirementFactory;
    /**
     * @var \Dopamedia\Completeness\Api\RequirementRepositoryInterface
     */
    private $repository;

    /**
     * @inheritDoc
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Dopamedia\Completeness\Api\Data\RequirementInterfaceFactory $requirementFactory,
        \Dopamedia\Completeness\Api\RequirementRepositoryInterface $repository
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
        $this->requirementFactory = $requirementFactory;
        $this->repository = $repository;
        parent::__construct($context, $resultPageFactory);
    }

    /**
     * @TODO::add validation
     *
     * @inheritDoc
     */
    public function execute()
    {
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $attributeSetId = $this->getRequest()->getParam('attribute_set_id');
        $typeId = $this->getRequest()->getParam('type_id');
        $storeId = $this->getRequest()->getParam('store_id');

        /** @var \Dopamedia\Completeness\Api\Data\RequirementInterface|\Magento\Framework\DataObject $requirement */
        $requirement = $this->requirementFactory->create()
            ->setAttributeId($attributeId)
            ->setAttributeSetId($attributeSetId)
            ->setTypeId($typeId)
            ->setStoreId($storeId);

        try {
            $this->repository->save($requirement);

            $this->messageManager->addSuccessMessage(__('You added the requirement.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var $block \Magento\Framework\View\Element\Messages */
        $block = $this->layoutFactory->create()->getMessagesBlock();
        $block->setMessages($this->messageManager->getMessages(true));

        $hasError = (bool)$this->messageManager->getMessages()->getCountByType(
            \Magento\Framework\Message\MessageInterface::TYPE_ERROR
        );

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData(
            [
                'messages' => $block->getGroupedHtml(),
                'error' => $hasError,
                'requirement' => $requirement->toArray()
            ]
        );
    }
}