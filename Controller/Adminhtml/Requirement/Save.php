<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 20.05.17
 */

namespace Dopamedia\Completeness\Controller\Adminhtml\Requirement;

use Dopamedia\Completeness\Controller\Adminhtml\Requirement;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;

class Save extends Requirement
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @inheritDoc
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\App\ResourceConnection $resource
    )
    {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        parent::__construct($context, $resultPageFactory);
    }


    /**
     * @inheritDoc
     */
    public function execute()
    {
        $attributeSetId = $this->getRequest()->getParam('attribute_set_id');
        $productTypeId = $this->getRequest()->getParam('product_type_id');
        $storeId = $this->getRequest()->getParam('store_id');
        $attributes = $this->getRequest()->getParam('attribute');

        $select = $this->connection->select()
            ->from(
                $this->resource->getTableName('catalog_product_completeness_requirement'),
                ['attribute_id']
            )
            ->where('attribute_set_id = ?', $attributeSetId)
            ->where('store_id = ?', $storeId)
            ->where('type_id = ?', $productTypeId);


        $oldAttributeIds = $this->connection->fetchCol($select);

        $newAttributeIds = array_keys(array_filter($attributes, function($value) {
            return !empty($value);
        }));

        $toDelete = array_diff($oldAttributeIds, $newAttributeIds);
        $toInsert = array_diff($newAttributeIds, $oldAttributeIds);

        if (!empty($toDelete)) {
            $this->connection->delete(
                $this->resource->getTableName('catalog_product_completeness_requirement'),
                [
                    'attribute_set_id = ?' => $attributeSetId,
                    'store_id = ?' => $storeId,
                    'type_id = ?' => $productTypeId,
                    'attribute_id IN (?)' => $toDelete
                ]
            );
        }

        if (!empty($toInsert)) {

            $insertData = [];

            foreach ($toInsert as $attributeId) {
                $insertData[] = [
                    'attribute_id' => $attributeId,
                    'attribute_set_id' => $attributeSetId,
                    'store_id' => $storeId,
                    'type_id' => $productTypeId
                ];
            }


            $this->connection->insertMultiple(
                $this->resource->getTableName('catalog_product_completeness_requirement'),
                $insertData
            );
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        if ($this->getRequest()->getParam('back')) {
            return $resultRedirect->setPath('*/*/edit', [
                'attribute_set_id' => $attributeSetId,
                'store_id' => $storeId,
                'product_type_id' => $productTypeId
            ]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}