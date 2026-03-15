<?php
/**
 * Clear SMS Logs Controller.
 *
 * Truncates the kwtsms_sms_log table to remove all log entries.
 * Restricted to POST requests only.
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\Result\Redirect;

class Clear extends Action
{
    /**
     * ACL resource identifier.
     */
    const ADMIN_RESOURCE = 'KwtSms_SmsIntegration::logs_clear';

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @param Context $context
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Truncate the SMS log table.
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName('kwtsms_sms_log');
            $connection->truncateTable($tableName);

            $this->messageManager->addSuccessMessage(__('SMS logs cleared.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Could not clear SMS logs: %1', $e->getMessage())
            );
        }

        return $resultRedirect->setPath('kwtsms/log/index');
    }

    /**
     * Only allow POST requests.
     *
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return parent::_isAllowed() && $this->getRequest()->isPost();
    }
}
