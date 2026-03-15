<?php
/**
 * kwtSMS Dashboard Controller.
 *
 * Renders the main kwtSMS admin dashboard page showing connection status,
 * balance, and quick statistics.
 *
 * @see \KwtSms\SmsIntegration\Block\Adminhtml\Dashboard
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\Page;

class Index extends Action
{
    /**
     * ACL resource identifier.
     */
    const ADMIN_RESOURCE = 'KwtSms_SmsIntegration::dashboard';

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
     * Render the dashboard page.
     *
     * @return Page
     */
    public function execute(): Page
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('KwtSms_SmsIntegration::dashboard');
        $resultPage->getConfig()->getTitle()->prepend(__('kwtSMS Dashboard'));

        return $resultPage;
    }
}
