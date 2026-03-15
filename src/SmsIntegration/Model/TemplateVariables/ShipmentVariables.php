<?php
/**
 * Shipment Template Variables.
 *
 * Resolves placeholder variables from a Magento shipment entity.
 * Extracts tracking information and merges with order variables
 * for SMS template rendering.
 *
 * @see \KwtSms\SmsIntegration\Model\TemplateVariables\OrderVariables
 * @see \KwtSms\SmsIntegration\Model\TemplateProcessor
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model\TemplateVariables;

use Magento\Sales\Model\Order\Shipment;

class ShipmentVariables
{
    /**
     * @var OrderVariables
     */
    private OrderVariables $orderVariables;

    /**
     * @param OrderVariables $orderVariables
     */
    public function __construct(OrderVariables $orderVariables)
    {
        $this->orderVariables = $orderVariables;
    }

    /**
     * Resolve template variables from a shipment entity.
     *
     * Returns tracking number and carrier name merged with all order variables
     * from the shipment's parent order.
     *
     * @param Shipment $shipment
     * @return array
     */
    public function resolve(Shipment $shipment): array
    {
        $tracks = $shipment->getAllTracks();
        $firstTrack = !empty($tracks) ? reset($tracks) : null;

        $shipmentVars = [
            'tracking_number' => $firstTrack ? (string) $firstTrack->getTrackNumber() : 'N/A',
            'carrier_name'    => $firstTrack ? (string) $firstTrack->getTitle() : '',
        ];

        $orderVars = $this->orderVariables->resolve($shipment->getOrder());

        return array_merge($orderVars, $shipmentVars);
    }
}
