<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.05.17
 */

namespace Dopamedia\Completeness\Model\ResourceModel\Requirement;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'requirement_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            'Dopamedia\Completeness\Model\Requirement',
            'Dopamedia\Completeness\Model\ResourceModel\Requirement'
        );
    }

}