<?php
/**
 * SMS Template Repository.
 *
 * Concrete implementation of SmsTemplateRepositoryInterface.
 * Handles CRUD operations and search-criteria listing for SMS template records.
 * Includes a convenience loader for fetching a template by its event type.
 *
 * @see \KwtSms\SmsIntegration\Api\SmsTemplateRepositoryInterface
 * @see \KwtSms\SmsIntegration\Api\Data\SmsTemplateInterface
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model;

use KwtSms\SmsIntegration\Api\Data\SmsTemplateInterface;
use KwtSms\SmsIntegration\Api\SmsTemplateRepositoryInterface;
use KwtSms\SmsIntegration\Model\ResourceModel\SmsTemplate as SmsTemplateResource;
use KwtSms\SmsIntegration\Model\ResourceModel\SmsTemplate\CollectionFactory;
use KwtSms\SmsIntegration\Model\SmsTemplateFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class SmsTemplateRepository implements SmsTemplateRepositoryInterface
{
    /**
     * @var SmsTemplateFactory
     */
    private SmsTemplateFactory $smsTemplateFactory;

    /**
     * @var SmsTemplateResource
     */
    private SmsTemplateResource $resource;

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
     * @param SmsTemplateFactory $smsTemplateFactory
     * @param SmsTemplateResource $resource
     * @param CollectionFactory $collectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        SmsTemplateFactory $smsTemplateFactory,
        SmsTemplateResource $resource,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->smsTemplateFactory = $smsTemplateFactory;
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(SmsTemplateInterface $smsTemplate): SmsTemplateInterface
    {
        try {
            /** @var SmsTemplate $smsTemplate */
            $this->resource->save($smsTemplate);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('Could not save the SMS template: %1', $e->getMessage()),
                $e
            );
        }

        return $smsTemplate;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $entityId): SmsTemplateInterface
    {
        $smsTemplate = $this->smsTemplateFactory->create();
        $this->resource->load($smsTemplate, $entityId);

        if (!$smsTemplate->getEntityId()) {
            throw new NoSuchEntityException(
                __('SMS template with ID "%1" does not exist.', $entityId)
            );
        }

        return $smsTemplate;
    }

    /**
     * @inheritdoc
     */
    public function getByEventType(string $eventType): SmsTemplateInterface
    {
        $smsTemplate = $this->smsTemplateFactory->create();
        $this->resource->load($smsTemplate, $eventType, SmsTemplateInterface::EVENT_TYPE);

        if (!$smsTemplate->getEntityId()) {
            throw new NoSuchEntityException(
                __('SMS template for event type "%1" does not exist.', $eventType)
            );
        }

        return $smsTemplate;
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
    public function delete(SmsTemplateInterface $smsTemplate): bool
    {
        try {
            /** @var SmsTemplate $smsTemplate */
            $this->resource->delete($smsTemplate);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __('Could not delete the SMS template: %1', $e->getMessage()),
                $e
            );
        }

        return true;
    }
}
