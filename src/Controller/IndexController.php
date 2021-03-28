<?php

namespace App\Controller;

use App\Service\Delivery\Cdek\CdekCourier;
use App\Service\Delivery\Cdek\CdekPickUp;
use App\Service\Delivery\DeliveryFactory;
use App\Service\Delivery\DeliveryManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    public function index()
    {
        $deliveries = [];

        $deliveryManager = new DeliveryManager();

        try {
            // Расчёт доставки "СДЕК курьер"
            $cdekCourierObj = DeliveryFactory::getObject(CdekCourier::DELIVERY_CODE);
            $cdekCourierResult = $deliveryManager->calculate($cdekCourierObj);

            if (!empty($cdekCourierResult)) {
                $deliveries[$cdekCourierResult['DELIVERY_CODE']] = $cdekCourierResult;
            }

            // Расчёт доставки "СДЕК Пункт выдачи"
            $cdekPickUpObj = DeliveryFactory::getObject(CdekPickUp::DELIVERY_CODE);
            $cdekPickUpResult = $deliveryManager->calculate($cdekPickUpObj);

            if (!empty($cdekPickUpResult)) {
                $deliveries[$cdekPickUpResult['DELIVERY_CODE']] = $cdekPickUpResult;
            }

        } catch (\Exception $e) {
            return new Response(
                'При расчёте доставки произошла ошибка.'
            );
        }

        return $this->render('index.html.twig', [
            'DELIVERIES' => $deliveries,
        ]);
    }
}