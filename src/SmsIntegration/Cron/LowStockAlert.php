<?php
/**
 * Low Stock Alert Cron Job.
 *
 * Checks product inventory levels and sends SMS alerts to admin phone numbers
 * when stock falls below the configured threshold.
 *
 * @see \KwtSms\SmsIntegration\Model\SmsSender
 * @see \KwtSms\SmsIntegration\Model\TemplateProcessor
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Cron;

use KwtSms\SmsIntegration\Model\Config;
use KwtSms\SmsIntegration\Model\SmsSender;
use KwtSms\SmsIntegration\Model\TemplateProcessor;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Item\CollectionFactory as StockItemCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class LowStockAlert
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var SmsSender
     */
    private SmsSender $smsSender;

    /**
     * @var TemplateProcessor
     */
    private TemplateProcessor $templateProcessor;

    /**
     * @var StockItemCollectionFactory
     */
    private StockItemCollectionFactory $stockItemCollectionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Config $config
     * @param SmsSender $smsSender
     * @param TemplateProcessor $templateProcessor
     * @param StockItemCollectionFactory $stockItemCollectionFactory
     * @param ProductRepositoryInterface $productRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        SmsSender $smsSender,
        TemplateProcessor $templateProcessor,
        StockItemCollectionFactory $stockItemCollectionFactory,
        ProductRepositoryInterface $productRepository,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->smsSender = $smsSender;
        $this->templateProcessor = $templateProcessor;
        $this->stockItemCollectionFactory = $stockItemCollectionFactory;
        $this->productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    /**
     * Check for low-stock products and send admin SMS alerts.
     *
     * Queries stock items that are still in stock but have a quantity at or
     * below the configured threshold. For each matching product, an SMS
     * alert is sent to every configured admin phone number.
     *
     * @return void
     */
    public function execute(): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        if (!$this->config->isIntegrationEnabled('admin_low_stock')) {
            return;
        }

        $adminPhones = $this->config->getAdminPhones();
        if (empty($adminPhones)) {
            return;
        }

        $threshold = $this->getLowStockThreshold();
        $alertCount = 0;

        try {
            /** @var \Magento\CatalogInventory\Model\ResourceModel\Stock\Item\Collection $collection */
            $collection = $this->stockItemCollectionFactory->create();
            $collection->addFieldToFilter('qty', ['lteq' => $threshold]);
            $collection->addFieldToFilter('qty', ['gt' => 0]);
            $collection->addFieldToFilter('is_in_stock', 1);

            foreach ($collection as $stockItem) {
                $productId = (int) $stockItem->getProductId();
                $productName = $this->getProductName($productId);
                $productSku = $this->getProductSku($productId);

                if ($productName === null) {
                    continue;
                }

                $variables = [
                    'product_name' => $productName,
                    'product_sku'  => $productSku ?? '',
                    'stock_qty'    => (int) $stockItem->getQty(),
                ];

                $message = $this->templateProcessor->render('admin_low_stock', $variables);
                if ($message === null) {
                    continue;
                }

                foreach ($adminPhones as $adminPhone) {
                    $this->smsSender->send(
                        $adminPhone,
                        $message,
                        'admin_low_stock',
                        (string) $productId,
                        'product'
                    );
                }

                $alertCount++;
            }
        } catch (\Exception $e) {
            $this->logger->error('kwtSMS: Low stock alert cron failed', [
                'message' => $e->getMessage(),
            ]);
        }

        $this->logger->info('kwtSMS: Low stock check completed', [
            'products_below_threshold' => $alertCount,
        ]);
    }

    /**
     * Get the configured low stock threshold.
     *
     * Falls back to 5 if no value is configured.
     *
     * @return int
     */
    private function getLowStockThreshold(): int
    {
        $value = $this->scopeConfig->getValue(
            'kwtsms/admin_alerts/low_stock_threshold',
            ScopeInterface::SCOPE_STORE
        );
        return $value !== null && $value !== '' ? (int) $value : 5;
    }

    /**
     * Load a product name by its ID.
     *
     * @param int $productId
     * @return string|null
     */
    private function getProductName(int $productId): ?string
    {
        try {
            $product = $this->productRepository->getById($productId);
            return $product->getName();
        } catch (\Exception $e) {
            $this->logger->debug('kwtSMS: Could not load product for low stock alert', [
                'product_id' => $productId,
                'message'    => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Load a product SKU by its ID.
     *
     * @param int $productId
     * @return string|null
     */
    private function getProductSku(int $productId): ?string
    {
        try {
            $product = $this->productRepository->getById($productId);
            return $product->getSku();
        } catch (\Exception $e) {
            return null;
        }
    }
}
