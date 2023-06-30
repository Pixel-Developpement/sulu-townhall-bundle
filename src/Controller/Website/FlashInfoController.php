<?php

namespace Pixel\TownHallBundle\Controller\Website;

use Doctrine\ORM\EntityManagerInterface;
use Pixel\TownHallBundle\Entity\FlashInfo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class FlashInfoController extends AbstractController
{
    /**
     * @Route("flash_info", name="flash_info", option={"expose"=true}, methods={"POST"})
     */
    public function flashInfo(EntityManagerInterface $entityManager): JsonResponse
    {
        $flashInfos = $entityManager->getRepository(FlashInfo::class)->findAll();
        if (empty($flashInfos)) {
            return new JsonResponse([
                "success" => true,
                "template" => $this->renderView("flash_info/empty.html.twig"),
            ]);
        }
        $activeFlashInfos = [];
        foreach ($flashInfos as $flashInfo) {
            if ($flashInfo->getIsActive()) {
                $activeFlashInfos[] = $flashInfo;
            }
        }
        if (empty($activeFlashInfos)) {
            return new JsonResponse([
                "success" => true,
                "template" => $this->renderView("flash_info/empty.html.twig"),
            ]);
        }
        return new JsonResponse([
            "success" => true,
            "template" => $this->renderView("flash_info/index.html.twig", [
                "activeFlashInfos" => $activeFlashInfos,
            ]),
        ]);
    }
}
