<?php
/**
 * SMS Log Repository.
 *
 * Concrete implementation of SmsLogRepositoryInterface.
 * Handles CRUD operations and search-criteria listing for SMS log records.
 *
 * @see \KwtSms\SmsIntegration\Api\SmsLogRepositoryInterface
 * @see \KwtSms\SmsIntegration\Api\Data\SmsLogInterface
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model;

use KwtSms\SmsIntegration\Api\Data\SmsLogInterface;
use KwtSms\SmsIntegration\Api\SmsLogRepositoryInterface;
use KwtSms\SmsIntegration\Model\ResourceModel\SmsLog as SmsLogResource;
use KwtSms\SmsIntegration\Model\ResourceModel\SmsLog\CollectionFactory;
use KwtSms\SmsIntegration\Model\SmsLogFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class SmsLogRepository implements SmsLogRepositoryInterface
{
    /**
     * @var SmsLogFactory
     */
    private SmsLogFactory $smsLogFactory;

    /**
     * @var SmsLogResource
     */
    private SmsLogResource $resource;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var SearchResultsInterfaceFactory
     */
    private SearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @param SmsLogFactory $smsLogFactory
     * @param SmsLogResource $resource
     * @param CollectionFactory $collectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        SmsLogFactory $smsLogFactory,
        SmsLogResource $resource,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->smsLogFactory = $smsLogFactory;
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(SmsLogInterface $smsLog): SmsLogInterface
    {
        try {
            /** @var SmsLog $smsLog */
            $this->resource->save($smsLog);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('Could not save the SMS log record: %1', $e->getMessage()),
                $e
            );
        }

        return $smsLog;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $entityId): SmsLogInterface
    {
        $smsLog = $this->smsLogFactory->create();
        $this->resource->load($smsLog, $entityId);

        if (!$smsLog->getEntityId()) {
            throw new NoSuchEntityException(
                __('SMS log record with ID "%1" does not exist.', $entityId)
            );
        }

        return $smsLog;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(SmsLogInterface $smsLog): bool
    {
        try {
            /** @var SmsLog $smsLog */
            $this->resource->delete($smsLog);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __('Could not delete the SMS log record: %1', $e->getMessage()),
                $e
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $entityId): bool
    {
        return $this->delete($this->getById($entityId));
    }
}
