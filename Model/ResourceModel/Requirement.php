<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.05.17
 */

namespace Dopamedia\Completeness\Model\ResourceModel;

class Requirement extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalog_product_completeness_requirement', 'requirement_id');
    }

}