<?php

namespace App\Service\Delivery;

use App\Service\Delivery\Cdek\CdekCourier;
use App\Service\Delivery\Cdek\CdekPickUp;

class DeliveryFactory
{
    public static function getObject(string $type): DeliveryInterface
    {
        $delivery = null;

        switch ($type) {
            case CdekCourier::DELIVERY_CODE:
                $delivery = new CdekCourier();
                break;
            case CdekPickUp::DELIVERY_CODE:
                $delivery = new CdekPickUp();
                break;
            default:
                throw new \Exception('Type fabric not found');
        }

        return $delivery;
    }
}