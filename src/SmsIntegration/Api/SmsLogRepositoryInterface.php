<?php
/**
 * SMS Log Repository Interface.
 *
 * Service contract for persisting and retrieving SMS log records.
 * Provides standard CRUD operations plus search-criteria based listing.
 *
 * @see \KwtSms\SmsIntegration\Model\SmsLogRepository
 * @see \KwtSms\SmsIntegration\Api\Data\SmsLogInterface
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Api;

use KwtSms\SmsIntegration\Api\Data\SmsLogInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface SmsLogRepositoryInterface
{
    /**
     * Save an SMS log record.
     *
     * @param SmsLogInterface $smsLog
     * @return SmsLogInterface
     * @throws CouldNotSaveException
     */
    public function save(SmsLogInterface $smsLog): SmsLogInterface;

    /**
     * Retrieve an SMS log record by its ID.
     *
     * @param int $entityId
     * @return SmsLogInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId): SmsLogInterface;

    /**
     * Retrieve a list of SMS log records matching the given search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Delete an SMS log record.
     *
     * @param SmsLogInterface $smsLog
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(SmsLogInterface $smsLog): bool;

    /**
     * Delete an SMS log record by its ID.
     *
     * @param int $entityId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $entityId): bool;
}
