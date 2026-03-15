<?php
/**
 * SMS Template Repository Interface.
 *
 * Service contract for persisting and retrieving SMS template records.
 * Includes a convenience method to load a template by its event type.
 *
 * @see \KwtSms\SmsIntegration\Model\SmsTemplateRepository
 * @see \KwtSms\SmsIntegration\Api\Data\SmsTemplateInterface
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Api;

use KwtSms\SmsIntegration\Api\Data\SmsTemplateInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface SmsTemplateRepositoryInterface
{
    /**
     * Save an SMS template record.
     *
     * @param SmsTemplateInterface $smsTemplate
     * @return SmsTemplateInterface
     * @throws CouldNotSaveException
     */
    public function save(SmsTemplateInterface $smsTemplate): SmsTemplateInterface;

    /**
     * Retrieve an SMS template by its ID.
     *
     * @param int $entityId
     * @return SmsTemplateInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId): SmsTemplateInterface;

    /**
     * Retrieve an SMS template by its event type.
     *
     * @param string $eventType
     * @return SmsTemplateInterface
     * @throws NoSuchEntityException
     */
    public function getByEventType(string $eventType): SmsTemplateInterface;

    /**
     * Retrieve a list of SMS templates matching the given search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Delete an SMS template record.
     *
     * @param SmsTemplateInterface $smsTemplate
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(SmsTemplateInterface $smsTemplate): bool;
}
