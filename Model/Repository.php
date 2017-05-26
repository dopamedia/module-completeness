<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 25.05.17
 */

namespace Dopamedia\Completeness\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class Repository implements \Dopamedia\Completeness\Api\RequirementRepositoryInterface
{

    /**
     * @var ResourceModel\Requirement
     */
    private $resource;

    /**
     * @var \Dopamedia\Completeness\Model\RequirementFactory
     */
    private $requirementFactory;

    /**
     * Repository constructor.
     * @param ResourceModel\Requirement $resource
     * @param RequirementFactory $requirementFactory
     */
    public function __construct(
        ResourceModel\Requirement $resource,
        RequirementFactory $requirementFactory
    ) {
        $this->resource = $resource;
        $this->requirementFactory = $requirementFactory;
    }

    /**
     * @inheritDoc
     */
    public function getById($requirementId)
    {
        $requirement = $this->requirementFactory->create();
        $this->resource->load($requirement, $requirementId);

        if (!$requirement->getId()) {
            throw new NoSuchEntityException(__('Requirement with id "%1" does not exist.', $requirementId));
        }

        return $requirement;
    }

    /**
     * @inheritDoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        // TODO: Implement getList() method.
    }

    /**
     * @inheritDoc
     */
    public function save(\Dopamedia\Completeness\Api\Data\RequirementInterface $requirement)
    {
        try {
            $this->resource->save($requirement);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $requirement;
    }

    /**
     * @inheritDoc
     */
    public function delete(\Dopamedia\Completeness\Api\Data\RequirementInterface $requirement)
    {
        try {
            $this->resource->delete($requirement);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($requirementId)
    {
        return $this->delete($this->getById($requirementId));
    }

}