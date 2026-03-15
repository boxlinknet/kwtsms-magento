<?php
/**
 * Clean Old Logs Cron Job.
 *
 * Removes expired SMS log entries from the kwtsms_sms_log table
 * based on the configured retention period.
 *
 * @see \KwtSms\SmsIntegration\Model\ResourceModel\SmsLog
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Cron;

use KwtSms\SmsIntegration\Api\Data\SmsLogInterface;
use KwtSms\SmsIntegration\Model\ResourceModel\SmsLog as SmsLogResource;
use Psr\Log\LoggerInterface;

class CleanOldLogs
{
    /**
     * Default log retention in days when no config value is set.
     */
    private const DEFAULT_RETENTION_DAYS = 90;

    /**
     * @var SmsLogResource
     */
    private SmsLogResource $smsLogResource;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param SmsLogResource $smsLogResource
     * @param LoggerInterface $logger
     */
    public function __construct(
        SmsLogResource $smsLogResource,
        LoggerInterface $logger
    ) {
        $this->smsLogResource = $smsLogResource;
        $this->logger = $logger;
    }

    /**
     * Delete SMS log entries older than the retention period.
     *
     * Calculates a cutoff date based on the retention days constant,
     * then removes all rows from kwtsms_sms_log with a created_at
     * timestamp before that cutoff.
     *
     * @return void
     */
    public function execute(): void
    {
        $days = self::DEFAULT_RETENTION_DAYS;
        $cutoff = date('Y-m-d H:i:s', strtotime(sprintf('-%d days', $days)));

        try {
            $connection = $this->smsLogResource->getConnection();
            $tableName = $this->smsLogResource->getMainTable();

            $deletedCount = $connection->delete(
                $tableName,
                [SmsLogInterface::CREATED_AT . ' < ?' => $cutoff]
            );

            $this->logger->info('kwtSMS: Cleaned old SMS log entries', [
                'deleted'      => $deletedCount,
                'cutoff_date'  => $cutoff,
                'retention_days' => $days,
            ]);
        } catch (\Exception $e) {
            $this->logger->error('kwtSMS: Failed to clean old SMS logs', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
