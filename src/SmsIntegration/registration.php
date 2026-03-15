<?php

/**
 * kwtSMS SMS Integration Module for Magento 2
 *
 * Provides SMS notifications, OTP verification, and admin alerts
 * via the kwtSMS gateway (kwtsms.com).
 *
 * @see https://www.kwtsms.com/doc/KwtSMS.com_API_Documentation_v41.pdf
 */

declare(strict_types=1);

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'KwtSms_SmsIntegration',
    __DIR__
);
