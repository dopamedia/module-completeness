<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 10.05.17
 */

namespace Dopamedia\Completeness\Test\Unit\Model\Indexer\Completeness;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Dopamedia\Completeness\Model\Indexer\Completeness\Product\Action\Full|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fullActionMock;

    /**
     * @var \Dopamedia\Completeness\Model\Indexer\Completeness\Product\Action\Rows|\PHPUnit_Framework_MockObject_MockObject
     */
    private $rowsActionMock;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $indexerRegistryMock;

    /**
     * @var \Dopamedia\Completeness\Model\Indexer\Completeness\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    private $model;

    protected function setUp()
    {
        $this->fullActionMock = $this->getMockBuilder('Dopamedia\Completeness\Model\Indexer\Completeness\Product\Action\Full')
            ->disableOriginalConstructor()
            ->getMock();

        $this->rowsActionMock = $this->getMockBuilder('Dopamedia\Completeness\Model\Indexer\Completeness\Product\Action\Rows')
            ->disableOriginalConstructor()
            ->getMock();

        $this->indexerRegistryMock = $this->getMock(
            'Magento\Framework\Indexer\IndexerRegistry',
            ['get'],
            [],
            '',
            false
        );

        $helper = new ObjectManager($this);

        $this->model = $helper->getObject(
            '\Dopamedia\Completeness\Model\Indexer\Completeness\Product',
            [
                'fullAction' => $this->fullActionMock,
                'rowsAction' => $this->rowsActionMock,
                'indexerRegistry' => $this->indexerRegistryMock
            ]
        );
    }

    public function testExecuteFull()
    {
        $this->fullActionMock->expects($this->any())->method('execute');
        $this->model->executeFull();
    }

    public function testExecuteList()
    {
        $ids = [1, 2, 3];
        $this->rowsActionMock->expects($this->any())->method('execute')->with($this->equalTo($ids));
        $this->model->executeList($ids);
    }

    public function testExecuteRow()
    {
        $expected = 5;
        $this->rowsActionMock->expects($this->any())->method('execute')->with($this->equalTo([$expected]));
        $this->model->executeRow($expected);
    }

    public function testExecute()
    {
        $ids = [4, 5, 6];
        $this->rowsActionMock->expects($this->any())->method('execute')->with($this->equalTo($ids));
        $this->model->executeList($ids);
    }

}
