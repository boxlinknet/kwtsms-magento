<?php
/**
 * SMS Template Edit Controller.
 *
 * Loads an SMS template by ID and renders the edit form page.
 * Redirects to the template listing with an error if the template is not found.
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\Controller\Result\Redirect;
use KwtSms\SmsIntegration\Api\SmsTemplateRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends Action
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
     * @var SmsTemplateRepositoryInterface
     */
    private SmsTemplateRepositoryInterface $templateRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param SmsTemplateRepositoryInterface $templateRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        SmsTemplateRepositoryInterface $templateRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->templateRepository = $templateRepository;
    }

    /**
     * Load template and render the edit form.
     *
     * @return Page|Redirect
     */
    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('id');

        if (!$id) {
            $this->messageManager->addErrorMessage(__('Template ID is required.'));
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('kwtsms/template/index');
        }

        try {
            $template = $this->templateRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This template no longer exists.'));
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('kwtsms/template/index');
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('KwtSms_SmsIntegration::templates_menu');
        $resultPage->getConfig()->getTitle()->prepend(
            __('Edit Template: %1', $template->getName())
        );

        return $resultPage;
    }
}
