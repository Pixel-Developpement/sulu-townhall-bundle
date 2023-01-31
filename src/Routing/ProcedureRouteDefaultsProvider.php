<?php

namespace Pixel\TownHallBundle\Routing;

use Pixel\TownHallBundle\Controller\Website\ProcedureController;
use Pixel\TownHallBundle\Entity\Procedure;
use Pixel\TownHallBundle\Repository\ProcedureRepository;
use Sulu\Bundle\RouteBundle\Routing\Defaults\RouteDefaultsProviderInterface;

class ProcedureRouteDefaultsProvider implements RouteDefaultsProviderInterface
{
    private ProcedureRepository $procedureRepository;

    public function __construct(ProcedureRepository $procedureRepository)
    {
        $this->procedureRepository = $procedureRepository;
    }

    /**
     * @return mixed[]
     */
    public function getByEntity($entityClass, $id, $locale, $object = null)
    {
        return [
            '_controller' => ProcedureController::class . '::indexAction',
            'procedure' => $object ?: $this->procedureRepository->findById((int) $id, $locale),
        ];
    }

    public function isPublished($entityClass, $id, $locale)
    {
        return true;
    }

    public function supports($entityClass)
    {
        return Procedure::class === $entityClass;
    }
}
