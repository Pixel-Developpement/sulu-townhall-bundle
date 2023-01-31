<?php

namespace Pixel\TownHallBundle\Controller\Website;

use Doctrine\ORM\EntityManagerInterface;
use Pixel\TownHallBundle\Entity\Setting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MeteoController extends AbstractController
{
    /**
     * @Route("meteo", name="meteo", options={"expose"=true}, methods={"POST"})
     */
    public function meteo(EntityManagerInterface $entityManager): JsonResponse
    {
        $setting = $entityManager->getRepository(Setting::class)->find(1);

        return new JsonResponse([
            "success" => true,
            "template" => $setting->getMeteo(),
        ]);
    }
}
