<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 25.05.17
 */

namespace Dopamedia\Completeness\Api;

/**
 * Interface RequirementRepositoryInterface
 * @api
 */
interface RequirementRepositoryInterface
{
    /**
     * @param int $requirementId
     * @return \Dopamedia\Completeness\Api\Data\RequirementInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($requirementId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Dopamedia\Completeness\Api\Data\RequirementSearchResultInterface
     * @throws \Magento\Framework\Exception\InputException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param Data\RequirementInterface $requirement
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Dopamedia\Completeness\Api\Data\RequirementInterface $requirement);

    /**
     * @param Data\RequirementInterface $requirement
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException If tax class with $taxClassId does not exist
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Dopamedia\Completeness\Api\Data\RequirementInterface $requirement);

    /**
     * @param int $requirementId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($requirementId);
}