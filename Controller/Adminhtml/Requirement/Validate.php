<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 26.05.17
 */

namespace Dopamedia\Completeness\Controller\Adminhtml\Requirement;

use Dopamedia\Completeness\Controller\Adminhtml\Requirement;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Validate extends Requirement
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Magento\Framework\DataObject
     */
    private $dataObject;

    /**
     * @inheritDoc
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataObject = $dataObjectFactory->create();
        parent::__construct($context, $resultPageFactory);
    }

    /**
     * @TODO::implement real validation
     *
     * @inheritDoc
     */
    public function execute()
    {
        $response = $this->dataObject->setData('error', 0);

        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($response);

        return $resultJson;
    }
}