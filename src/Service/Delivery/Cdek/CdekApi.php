<?php

namespace App\Service\Delivery\Cdek;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CdekApi
{
    // Auth params
    const AUTH_PARAMS = [
        'TYPE_KEY' => 'grant_type',
        'TYPE_VALUE' => 'client_credentials',
        'CLIENT_KEY' => 'client_id',
        'CLIENT_VALUE' => 'EMscd6r9JnFiQ3bLoyjJY6eM78JrJceI',
        'SECRET_KEY' => 'client_secret',
        'SECRET_VALUE' => 'PjLZkKBHEiLK3YsjtNrt3TGNG0ahs3kG',
    ];

    // Codes
    const SUCCESS = 200;

    // Api URL
    const BASE_URI = 'https://api.edu.cdek.ru/v2';
    // Methods
    const TOKEN_METHOD = '/oauth/token';
    const CALC_TARIFF_METHOD = '/calculator/tariff';

    private static $accessToken = '';

    public function __construct()
    {
        if (empty(self::$accessToken)) {

            $this->setAccessToken();
        }
    }

    /**
     * Расчёт цены доставки
     *
     * @param $deliveryParams
     * @return array
     * @throws GuzzleException
     */
    public function calculate(array $deliveryParams): array
    {
        $calculateResult = [];

        if (!empty(self::$accessToken)) {

            $client = new Client();
            $result = $client->request('POST', self::BASE_URI . self::CALC_TARIFF_METHOD, [
                'headers' => [
                    'Authorization' => 'Bearer ' . self::$accessToken
                ],
                'json' => $deliveryParams,
            ]);

            if ($result->getStatusCode() == self::SUCCESS) {

                $resultContent = json_decode($result->getBody()->getContents(), true);

                if (!empty($resultContent)) {

                    $calculateResult = [
                        'PRICE' => $resultContent['total_sum'],
                        'PERIOD_MIN' => $resultContent['period_min'],
                        'PERIOD_MAX' => $resultContent['period_max'],
                    ];
                }
            } else {

                throw new \Exception('Не удалось расчитать стоимость доставки');
            }

        }

        return $calculateResult;
    }

    /**
     * Получение токена авторизации
     */
    private function setAccessToken(): void
    {
        $client = new Client();
        try {
            $result = $client->request('POST', self::BASE_URI . self::TOKEN_METHOD, [
                'form_params' => [
                    self::AUTH_PARAMS['TYPE_KEY'] => self::AUTH_PARAMS['TYPE_VALUE'],
                    self::AUTH_PARAMS['CLIENT_KEY'] => self::AUTH_PARAMS['CLIENT_VALUE'],
                    self::AUTH_PARAMS['SECRET_KEY'] => self::AUTH_PARAMS['SECRET_VALUE'],
                ],
            ]);

            if ($result->getStatusCode() == self::SUCCESS) {

                $resultContent = json_decode($result->getBody()->getContents(), true);

                self::$accessToken = $resultContent['access_token'];
            }
        } catch (GuzzleException $e) {

            throw new \Exception('Не удалось получить токен авторизации: ' . $e->getMessage());
        }
    }
}