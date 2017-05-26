<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 25.05.17
 */

namespace Dopamedia\Completeness\Test\Unit\Controller\Adminhtml\Requirement;

use Dopamedia\Completeness\Controller\Adminhtml\Requirement\Delete;

class DeleteTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Dopamedia\Completeness\Controller\Adminhtml\Requirement\Delete
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
     * @var \Magento\Backend\Model\View\Result\RedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectFactoryMock;

    /**
     * @var \Magento\Backend\Model\View\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Dopamedia\Completeness\Model\Repository
     */
    protected $repositoryMock;

    /**
     * @var int
     */
    protected $requirementId = 1;

    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->messageManagerMock = $this->getMock('Magento\Framework\Message\ManagerInterface', [], [], '', false);

        $this->requestMock = $this->getMockForAbstractClass(
            'Magento\Framework\App\RequestInterface',
            [],
            '',
            false,
            true,
            true,
            ['getParam']
        );

        $this->repositoryMock = $this->getMockBuilder('Dopamedia\Completeness\Model\Repository')
            ->disableOriginalConstructor()
            ->setMethods(['deleteById'])
            ->getMock();

        $this->resultRedirectMock = $this->getMockBuilder('Magento\Backend\Model\View\Result\Redirect')
            ->setMethods(['setPath'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultRedirectFactoryMock = $this->getMockBuilder('Magento\Backend\Model\View\Result\RedirectFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->resultRedirectFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->resultRedirectMock);

        $this->contextMock = $this->getMock(
            '\Magento\Backend\App\Action\Context',
            [],
            [],
            '',
            false
        );

        $this->contextMock->expects($this->any())->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->expects($this->any())->method('getMessageManager')->willReturn($this->messageManagerMock);
        $this->contextMock->expects($this->any())
            ->method('getResultRedirectFactory')
            ->willReturn($this->resultRedirectFactoryMock);

        $this->controllerMock = $this->objectManager->getObject(
            'Dopamedia\Completeness\Controller\Adminhtml\Requirement\Delete',
            [
                'context' => $this->contextMock,
                'repository' => $this->repositoryMock
            ]
        );
    }

    public function testExecute()
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->willReturn($this->requirementId);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('You deleted the requirement'));

        $this->messageManagerMock->expects($this->never())
            ->method('addErrorMessage');

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->repositoryMock->expects($this->once())
            ->method('deleteById')
            ->with($this->requirementId);

        $this->assertSame($this->resultRedirectMock, $this->controllerMock->execute());
    }

    public function testExecuteNoId()
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->willReturn(null);

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('We can\'t find a requirement to delete.'));

        $this->messageManagerMock->expects($this->never())
            ->method('addSuccess');

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->controllerMock->execute());
    }

    public function testExecuteDeleteByIdThrowsException()
    {
        $errorMessage = 'Cant\'t delete the requirement';

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->willReturn($this->requirementId);

        $this->repositoryMock->expects($this->once())
            ->method('deleteById')
            ->with($this->requirementId)
            ->willThrowException(new \Exception(__($errorMessage)));

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($errorMessage);
        $this->messageManagerMock->expects($this->never())
            ->method('addSuccessMessage');

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->controllerMock->execute());
    }
}
