<?php

namespace App\Service\Delivery\Cdek;

use App\Service\Delivery\DeliveryInterface;
use Exception;
use GuzzleHttp\Exception\GuzzleException;

class CdekCourier implements DeliveryInterface
{
    const DELIVERY_NAME = 'Курьер СДЕК';
    const DELIVERY_CODE = 'CDEK_COURIER';
    const TARIFF_CODE = 11;

    /**
     * Расчёт цены доставки
     *
     * @return array
     * @throws Exception
     */
    public function calculate(): array
    {
        $result = [];

        $calculateParams = [
            'tariff_code' => self::TARIFF_CODE,
            'from_location' => [
                'code' => 270,
            ],
            'to_location' => [
                'code' => 44,
            ],
            'packages' => [
                'height' => 10,
                'length' => 10,
                'weight' => 4000,
                'width' => 10,
            ],
        ];

        try {
            $cdekApi = new CdekApi();
            $resultApi = $cdekApi->calculate($calculateParams);

            if (!empty($resultApi)) {

                $result = [
                    'DELIVERY_NAME' => self::DELIVERY_NAME,
                    'DELIVERY_CODE' => self::DELIVERY_CODE,
                    'PRICE' => $resultApi['PRICE'],
                    'PERIOD_MIN' => $resultApi['PERIOD_MIN'],
                    'PERIOD_MAX' => $resultApi['PERIOD_MAX'],
                ];
            }
        } catch (Exception | GuzzleException $e) {

            throw new Exception($e->getMessage());
        }

        return $result;
    }
}