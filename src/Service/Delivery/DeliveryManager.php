<?php

namespace App\Service\Delivery;

class DeliveryManager
{
    public function calculate(DeliveryInterface $delivery)
    {
       return $delivery->calculate();
    }
}