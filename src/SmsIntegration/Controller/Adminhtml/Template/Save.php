<?php
/**
 * SMS Template Save Controller.
 *
 * Handles POST submissions from the template edit form.
 * Updates the template fields and persists them via the repository.
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use KwtSms\SmsIntegration\Api\SmsTemplateRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;

class Save extends Action
{
    /**
     * ACL resource identifier.
     */
    const ADMIN_RESOURCE = 'KwtSms_SmsIntegration::templates';

    /**
     * @var SmsTemplateRepositoryInterface
     */
    private SmsTemplateRepositoryInterface $templateRepository;

    /**
     * @param Context $context
     * @param SmsTemplateRepositoryInterface $templateRepository
     */
    public function __construct(
        Context $context,
        SmsTemplateRepositoryInterface $templateRepository
    ) {
        parent::__construct($context);
        $this->templateRepository = $templateRepository;
    }

    /**
     * Save template data from POST.
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();
        if (empty($data)) {
            $this->messageManager->addErrorMessage(__('No data to save.'));
            return $resultRedirect->setPath('kwtsms/template/index');
        }

        $id = (int) ($data['entity_id'] ?? 0);
        if (!$id) {
            $this->messageManager->addErrorMessage(__('Template ID is missing.'));
            return $resultRedirect->setPath('kwtsms/template/index');
        }

        try {
            $template = $this->templateRepository->getById($id);

            if (isset($data['message_en'])) {
                $template->setMessageEn((string) $data['message_en']);
            }
            if (isset($data['message_ar'])) {
                $template->setMessageAr((string) $data['message_ar']);
            }

            $this->templateRepository->save($template);

            $this->messageManager->addSuccessMessage(__('Template saved successfully.'));
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This template no longer exists.'));
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage(
                __('Could not save template: %1', $e->getMessage())
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('An error occurred while saving the template: %1', $e->getMessage())
            );
        }

        return $resultRedirect->setPath('kwtsms/template/index');
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
