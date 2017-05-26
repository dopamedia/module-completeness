<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 25.05.17
 */

namespace Dopamedia\Completeness\Test\Unit\Controller\Adminhtml\Requirement;

class MassDeleteTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Dopamedia\Completeness\Controller\Adminhtml\Requirement\MassDelete
     */
    protected $controllerMock;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\Controller\ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var \Magento\Backend\Model\View\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterMock;

    /**
     * @var \Dopamedia\Completeness\Model\ResourceModel\Requirement\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactoryMock;

    /**
     * @var \Dopamedia\Completeness\Model\ResourceModel\Requirement\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requirementCollectionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Dopamedia\Completeness\Model\Repository
     */
    protected $repositoryMock;

    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->messageManagerMock = $this->getMock('Magento\Framework\Message\ManagerInterface', [], [], '', false);

        $this->resultFactoryMock = $this->getMockBuilder('Magento\Framework\Controller\ResultFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultRedirectMock = $this->getMockBuilder('Magento\Backend\Model\View\Result\Redirect')
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultFactoryMock->expects($this->any())
            ->method('create')
            ->with(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT, [])
            ->willReturn($this->resultRedirectMock);

        $this->contextMock = $this->getMock('\Magento\Backend\App\Action\Context', [], [], '', false);

        $this->filterMock = $this->getMockBuilder('Magento\Ui\Component\MassAction\Filter')
            ->disableOriginalConstructor()
            ->getMock();

        $this->contextMock->expects($this->any())->method('getMessageManager')->willReturn($this->messageManagerMock);
        $this->contextMock->expects($this->any())->method('getResultFactory')->willReturn($this->resultFactoryMock);

        $this->collectionFactoryMock = $this->getMock(
            'Dopamedia\Completeness\Model\ResourceModel\Requirement\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->requirementCollectionMock = $this->getMock('Dopamedia\Completeness\Model\ResourceModel\Requirement\Collection', [], [], '', false);

        $this->repositoryMock = $this->getMockBuilder('Dopamedia\Completeness\Model\Repository')
            ->disableOriginalConstructor()
            ->setMethods(['delete'])
            ->getMock();

        $this->controllerMock = $this->objectManager->getObject(
            'Dopamedia\Completeness\Controller\Adminhtml\Requirement\MassDelete',
            [
                'context' => $this->contextMock,
                'filter' => $this->filterMock,
                'collectionFactory' => $this->collectionFactoryMock,
                'repository' => $this->repositoryMock
            ]
        );
    }

    public function testExecute()
    {
        $deleteRequirementsCount = 2;

        $collection = [
            $this->getRequirementMock(),
            $this->getRequirementMock()
        ];

        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->requirementCollectionMock);

        $this->filterMock->expects($this->once())
            ->method('getCollection')
            ->with($this->requirementCollectionMock)
            ->willReturn($this->requirementCollectionMock);

        $this->requirementCollectionMock->expects($this->once())
            ->method('getSize')->willReturn($deleteRequirementsCount);

        $this->requirementCollectionMock->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator($collection));

        $this->repositoryMock->expects($this->exactly(2))
            ->method('delete');


        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('A total of %1 record(s) have been deleted.', $deleteRequirementsCount));

        $this->messageManagerMock->expects($this->never())->method('addError');

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->controllerMock->execute());

    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getRequirementMock()
    {
        return $this->getMock('Dopamedia\Completeness\Model\Requirement', [], [], '', false);
    }

}
