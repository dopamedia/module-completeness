<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 20.05.17
 */

namespace Dopamedia\Completeness\Controller\Adminhtml\Requirement;

use Dopamedia\Completeness\Controller\Adminhtml\Requirement;
use Magento\Framework\App\ResponseInterface;

class Save extends Requirement
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        var_dump($this->getRequest()->getParams());
        die();

        die('in here');
        // TODO: Implement execute() method.
    }

}