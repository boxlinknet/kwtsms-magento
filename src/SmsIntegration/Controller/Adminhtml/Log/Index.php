<?php
/**
 * SMS Log Listing Controller.
 *
 * Renders the SMS log listing page in the admin panel.
 * The listing grid is powered by the kwtsms_sms_log_listing UI component.
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\Page;

class Index extends Action
{
    /**
     * ACL resource identifier.
     */
    const ADMIN_RESOURCE = 'KwtSms_SmsIntegration::logs';

    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Render the SMS logs listing page.
     *
     * @return Page
     */
    public function execute(): Page
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('KwtSms_SmsIntegration::logs_menu');
        $resultPage->getConfig()->getTitle()->prepend(__('SMS Logs'));

        return $resultPage;
    }
}
