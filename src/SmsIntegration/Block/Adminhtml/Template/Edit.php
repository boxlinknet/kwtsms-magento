<?php
/**
 * SMS Template Edit Block.
 *
 * Provides data for the template edit form, including the loaded template,
 * form URLs, and available placeholder variables.
 *
 * @see \KwtSms\SmsIntegration\Api\SmsTemplateRepositoryInterface
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Block\Adminhtml\Template;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use KwtSms\SmsIntegration\Api\SmsTemplateRepositoryInterface;
use KwtSms\SmsIntegration\Api\Data\SmsTemplateInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends Template
{
    /**
     * @var SmsTemplateRepositoryInterface
     */
    private SmsTemplateRepositoryInterface $templateRepository;

    /**
     * @var SmsTemplateInterface|null|false
     */
    private $loadedTemplate = false;

    /**
     * @param Context $context
     * @param SmsTemplateRepositoryInterface $templateRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        SmsTemplateRepositoryInterface $templateRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->templateRepository = $templateRepository;
    }

    /**
     * Get the template being edited.
     *
     * @return SmsTemplateInterface|null
     */
    public function getTemplate(): ?SmsTemplateInterface
    {
        if ($this->loadedTemplate === false) {
            $id = (int) $this->getRequest()->getParam('id');
            if ($id) {
                try {
                    $this->loadedTemplate = $this->templateRepository->getById($id);
                } catch (NoSuchEntityException $e) {
                    $this->loadedTemplate = null;
                }
            } else {
                $this->loadedTemplate = null;
            }
        }

        return $this->loadedTemplate;
    }

    /**
     * Get the form save URL.
     *
     * @return string
     */
    public function getSaveUrl(): string
    {
        return $this->getUrl('kwtsms/template/save');
    }

    /**
     * Get the back (listing) URL.
     *
     * @return string
     */
    public function getBackUrl(): string
    {
        return $this->getUrl('kwtsms/template/index');
    }

    /**
     * Get available placeholders based on the template event type.
     *
     * @return array
     */
    public function getAvailablePlaceholders(): array
    {
        $template = $this->getTemplate();
        if ($template === null) {
            return [];
        }

        $eventType = $template->getEventType();

        $orderPlaceholders = [
            '{{order_id}}',
            '{{order_status}}',
            '{{total}}',
            '{{currency}}',
            '{{customer_name}}',
            '{{customer_email}}',
            '{{store_name}}',
        ];

        $shipmentPlaceholders = [
            '{{order_id}}',
            '{{tracking_number}}',
            '{{carrier_title}}',
            '{{customer_name}}',
            '{{store_name}}',
        ];

        $customerPlaceholders = [
            '{{customer_name}}',
            '{{customer_email}}',
            '{{store_name}}',
        ];

        $adminOrderPlaceholders = [
            '{{order_id}}',
            '{{total}}',
            '{{currency}}',
            '{{customer_name}}',
            '{{customer_email}}',
            '{{store_name}}',
        ];

        $stockPlaceholders = [
            '{{product_name}}',
            '{{product_sku}}',
            '{{stock_qty}}',
            '{{store_name}}',
        ];

        switch ($eventType) {
            case 'order_new':
            case 'order_status_change':
            case 'order_invoiced':
            case 'order_refunded':
            case 'order_cancelled':
                return $orderPlaceholders;

            case 'order_shipped':
                return $shipmentPlaceholders;

            case 'customer_welcome':
                return $customerPlaceholders;

            case 'admin_new_order':
                return $adminOrderPlaceholders;

            case 'admin_new_customer':
                return $customerPlaceholders;

            case 'admin_low_stock':
                return $stockPlaceholders;

            default:
                return ['{{store_name}}'];
        }
    }
}
