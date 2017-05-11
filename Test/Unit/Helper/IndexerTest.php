<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 11.05.17
 */

namespace Dopamedia\Completeness\Test\Unit\Helper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class IndexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\AbstractDb|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Magento\Framework\DB\Adapter\Pdo\Mysql|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $connectionMock;

    /**
     * @var \Magento\Framework\DB\Select|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $selectMock;

    /**
     * @var \Dopamedia\Completeness\Helper\Indexer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    protected function setUp()
    {
        $this->resourceMock = $this->getMock('Magento\Framework\App\ResourceConnection', [], [], '', false);
        $this->connectionMock = $this->getMock('Magento\Framework\DB\Adapter\Pdo\Mysql', [], [], '', false);
        $renderer = $this->getMock('Magento\Framework\DB\Select\SelectRenderer', [], [], '', false);

        $this->resourceMock
            ->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->connectionMock));

        $this->selectMock = $this->getMock(
            'Magento\Framework\DB\Select',
            ['from', 'join', 'where'],
            [$this->connectionMock, $renderer]
        );

        $this->connectionMock
            ->expects($this->any())
            ->method('select')
            ->will($this->returnValue($this->selectMock));

        $helper = new ObjectManager($this);

        $this->model = $helper->getObject(
            '\Dopamedia\Completeness\Helper\Indexer',
            [
                'resource' => $this->resourceMock
            ]
        );
    }

    public function testGetTable()
    {
        $tableName = 'table_name';

        $this->resourceMock
            ->expects($this->any())
            ->method('getTableName')
            ->will($this->returnValue($tableName));

        $this->assertEquals($tableName, $this->model->getTable($tableName));
    }

    public function testGetAttributeCodes()
    {
        $this->resourceMock
            ->expects($this->any())
            ->method('getTableName')
            ->will($this->returnValue(''));


        $this->selectMock->expects($this->once())->method('from')->will($this->returnSelf());
        $this->selectMock->expects($this->once())->method('join')->will($this->returnSelf());
        
        $this->connectionMock->expects($this->once())->method('fetchAll')->willReturn(
            [
                [
                    'attribute_id' => 1,
                    'attribute_code' => 'first'
                ],
                [
                    'attribute_id' => 2,
                    'attribute_code' => 'second'
                ]
            ]
        );


        $this->assertEquals(
            [1 => 'first', 2 => 'second'],
            $this->model->getAttributeCodes(0)
        );
    }
}
