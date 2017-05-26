<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 25.05.17
 */

namespace Dopamedia\Completeness\Test\Unit\Model;

use Dopamedia\Completeness\Model\Repository;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Dopamedia\Completeness\Api\Data\RequirementInterface
     */
    private $requirement;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Dopamedia\Completeness\Model\ResourceModel\Requirement
     */
    private $resource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Dopamedia\Completeness\Model\Repository
     */
    private $repository;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->requirement = $this->getMockBuilder('Dopamedia\Completeness\Model\Requirement')
            ->disableOriginalConstructor()
            ->getMock();

        $this->resource = $this->getMockBuilder('Dopamedia\Completeness\Model\ResourceModel\Requirement')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Dopamedia\Completeness\Model\RequirementFactory $factory */
        $factory = $this->getMockBuilder('Dopamedia\Completeness\Model\RequirementFactory')
            ->setMethods(['create'])
            ->getMock();

        $factory->expects($this->any())
            ->method('create')
            ->willReturn($this->requirement);

        $this->repository = new Repository(
            $this->resource,
            $factory
        );
    }

    public function testGetById()
    {
        $requirementId = '123';

        $this->requirement->expects($this->once())
            ->method('getId')
            ->willReturn($requirementId);

        $this->resource->expects($this->once())
            ->method('load')
            ->with($this->requirement, $requirementId)
            ->willReturn($this->requirement);

        $this->assertEquals($this->requirement, $this->repository->getById($requirementId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetByIdException()
    {
        $requirementId = '123';

        $this->requirement->expects($this->once())
            ->method('getId')
            ->willReturn(false);

        $this->resource->expects($this->once())
            ->method('load')
            ->with($this->requirement, $requirementId)
            ->willReturn($this->requirement);

        $this->repository->getById($requirementId);
    }

    public function testSave()
    {
        $this->resource->expects($this->once())
            ->method('save')
            ->with($this->requirement)
            ->willReturnSelf();
        $this->assertEquals($this->requirement, $this->repository->save($this->requirement));
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testSaveException()
    {
        $this->resource->expects($this->once())
            ->method('save')
            ->with($this->requirement)
            ->willThrowException(new \Exception());
        $this->repository->save($this->requirement);
    }

    public function testDelete()
    {
        $this->resource->expects($this->once())
            ->method('delete')
            ->with($this->requirement);

        $this->repository->delete($this->requirement);
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function testDeleteException()
    {
        $this->resource->expects($this->once())
            ->method('delete')
            ->with($this->requirement)
            ->willThrowException(new \Exception());
        $this->repository->delete($this->requirement);
    }

    public function testDeleteById()
    {
        $requirementId = '123';

        $this->requirement->expects($this->once())
            ->method('getId')
            ->willReturn(true);

        $this->resource->expects($this->once())
            ->method('load')
            ->with($this->requirement, $requirementId)
            ->willReturn($this->requirement);

        $this->resource->expects($this->once())
            ->method('delete')
            ->with($this->requirement)
            ->willReturnSelf();

        $this->assertTrue($this->repository->deleteById($requirementId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testDeleteByIdException()
    {
        $requirementId = '123';

        $this->requirement->expects($this->once())
            ->method('getId')
            ->willReturn(false);

        $this->resource->expects($this->once())
            ->method('load')
            ->with($this->requirement, $requirementId)
            ->willReturn($this->requirement);

        $this->repository->getById($requirementId);
    }
}
