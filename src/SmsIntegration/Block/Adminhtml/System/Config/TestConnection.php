<?php
/**
 * kwtSMS Integration for Magento 2
 * Test Connection button for system configuration
 */

declare(strict_types=1);

namespace KwtSms\SmsIntegration\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;

class TestConnection extends Field
{
    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Remove scope label
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return AJAX URL for test connection controller
     *
     * @return string
     */
    public function getAjaxUrl(): string
    {
        return $this->getUrl('kwtsms/gateway/testconnection');
    }

    /**
     * Generate button HTML with inline AJAX handler
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $ajaxUrl = $this->getAjaxUrl();

        return <<<HTML
<button id="kwtsms_test_connection_button" type="button" class="action-default scalable">
    <span>Login</span>
</button>
<span id="kwtsms_test_connection_result" style="margin-left: 10px;"></span>
<script>
require(['jquery'], function($) {
    $('#kwtsms_test_connection_button').on('click', function() {
        var button = $(this);
        var resultSpan = $('#kwtsms_test_connection_result');

        button.prop('disabled', true);
        resultSpan.text('Logging in...').css('color', '#333');

        $.ajax({
            url: '{$ajaxUrl}',
            type: 'POST',
            dataType: 'json',
            data: {
                form_key: window.FORM_KEY
            },
            success: function(response) {
                if (response.success) {
                    resultSpan.text('Logged in! Balance: ' + response.balance).css('color', 'green');
                } else {
                    resultSpan.text(response.message || 'Login failed.').css('color', 'red');
                }
            },
            error: function() {
                resultSpan.text('Request failed. Check your network and try again.').css('color', 'red');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });
});
</script>
HTML;
    }
}
