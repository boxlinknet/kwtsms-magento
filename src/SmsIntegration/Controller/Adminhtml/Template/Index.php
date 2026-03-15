<?php
/**
 * SMS Template Listing Controller.
 *
 * Renders the SMS template listing page in the admin panel.
 * The listing grid is powered by the kwtsms_sms_template_listing UI component.
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\Page;

class Index extends Action
{
    /**
     * ACL resource identifier.
     */
    const ADMIN_RESOURCE = 'KwtSms_SmsIntegration::templates';

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
     * Render the SMS template listing page.
     *
     * @return Page
     */
    public function execute(): Page
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('KwtSms_SmsIntegration::templates_menu');
        $resultPage->getConfig()->getTitle()->prepend(__('SMS Templates'));

        return $resultPage;
    }
}
