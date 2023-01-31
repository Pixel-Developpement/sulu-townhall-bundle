<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Trash;

use Doctrine\ORM\EntityManagerInterface;
use Pixel\TownHallBundle\Admin\ProcedureAdmin;
use Pixel\TownHallBundle\Domain\Event\ProcedureRestoredEvent;
use Pixel\TownHallBundle\Entity\Procedure;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\CategoryBundle\Entity\CategoryInterface;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Bundle\RouteBundle\Entity\Route;
use Sulu\Bundle\TrashBundle\Application\DoctrineRestoreHelper\DoctrineRestoreHelperInterface;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfiguration;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfigurationProviderInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\RestoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\StoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Domain\Model\TrashItemInterface;
use Sulu\Bundle\TrashBundle\Domain\Repository\TrashItemRepositoryInterface;

class ProcedureTrashItemHandler implements StoreTrashItemHandlerInterface, RestoreTrashItemHandlerInterface, RestoreConfigurationProviderInterface
{
    private TrashItemRepositoryInterface $trashItemRepository;

    private EntityManagerInterface $entityManager;

    private DoctrineRestoreHelperInterface $doctrineRestoreHelper;

    private DomainEventCollectorInterface $domainEventCollector;

    public function __construct(
        TrashItemRepositoryInterface $trashItemRepository,
        EntityManagerInterface $entityManager,
        DoctrineRestoreHelperInterface $doctrineRestoreHelper,
        DomainEventCollectorInterface $domainEventCollector
    ) {
        $this->trashItemRepository = $trashItemRepository;
        $this->entityManager = $entityManager;
        $this->doctrineRestoreHelper = $doctrineRestoreHelper;
        $this->domainEventCollector = $domainEventCollector;
    }

    public static function getResourceKey(): string
    {
        return Procedure::RESOURCE_KEY;
    }

    public function store(object $resource, array $options = []): TrashItemInterface
    {
        $cover = $resource->getCover();
        $document = $resource->getDocument();
        $category = $resource->getCategory();

        $data = [
            "title" => $resource->getTitle(),
            "description" => $resource->getDescription(),
            "slug" => $resource->getRoutePath(),
            "seo" => $resource->getSeo(),
            "coverId" => $cover ? $cover->getId() : null,
            "state" => $resource->getState(),
            "externalLink" => $resource->getExternalLink(),
            "documentId" => $document ? $document->getId() : null,
            "categoryId" => $category ? $category->getId() : null,
        ];

        return $this->trashItemRepository->create(
            Procedure::RESOURCE_KEY,
            (string) $resource->getId(),
            $resource->getTitle(),
            $data,
            null,
            $options,
            Procedure::SECURITY_CONTEXT,
            null,
            null
        );
    }

    public function restore(TrashItemInterface $trashItem, array $restoreFormData = []): object
    {
        $data = $trashItem->getRestoreData();
        $procedureId = (int) $trashItem->getResourceId();
        $procedure = new Procedure();
        $procedure->setTitle($data['title']);
        $procedure->setDescription($data['description']);
        $procedure->setRoutePath($data['slug']);
        $procedure->setSeo($data['seo']);
        if ($data['coverId']) {
            $procedure->setCover($this->entityManager->find(MediaInterface::class, $data['coverId']));
        }
        $procedure->setState($data['state']);
        $procedure->setExternalLink($data['externalLink']);
        if ($data['documentId']) {
            $procedure->setDocument($this->entityManager->find(MediaInterface::class, $data['documentId']));
        }
        $procedure->setCategory($this->entityManager->find(CategoryInterface::class, $data['categoryId']));
        $this->domainEventCollector->collect(
            new ProcedureRestoredEvent($procedure, $data)
        );

        $this->doctrineRestoreHelper->persistAndFlushWithId($procedure, $procedureId);
        $this->createRoute($this->entityManager, $procedureId, $procedure->getRoutePath(), Procedure::class);
        $this->entityManager->flush();
        return $procedure;
    }

    private function createRoute(EntityManagerInterface $manager, int $id, string $slug, string $class): void
    {
        $route = new Route();
        $route->setPath($slug);
        $route->setLocale('fr');
        $route->setEntityClass($class);
        $route->setEntityId((string) $id);
        $route->setHistory(false);
        $route->setCreated(new \DateTime());
        $route->setChanged(new \DateTime());
        $manager->persist($route);
    }

    public function getConfiguration(): RestoreConfiguration
    {
        return new RestoreConfiguration(null, ProcedureAdmin::EDIT_FORM_VIEW, [
            'id' => 'id',
        ]);
    }
}
