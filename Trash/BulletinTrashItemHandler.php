<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Trash;

use Doctrine\ORM\EntityManagerInterface;
use Pixel\TownHallBundle\Admin\BulletinAdmin;
use Pixel\TownHallBundle\Domain\Event\BulletinRestoredEvent;
use Pixel\TownHallBundle\Entity\Bulletin;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Bundle\TrashBundle\Application\DoctrineRestoreHelper\DoctrineRestoreHelperInterface;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfiguration;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfigurationProviderInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\RestoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\StoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Domain\Model\TrashItemInterface;
use Sulu\Bundle\TrashBundle\Domain\Repository\TrashItemRepositoryInterface;

class BulletinTrashItemHandler implements StoreTrashItemHandlerInterface, RestoreTrashItemHandlerInterface, RestoreConfigurationProviderInterface
{
    private TrashItemRepositoryInterface $trashItemRepository;
    private EntityManagerInterface $entityManager;
    private DoctrineRestoreHelperInterface $doctrineRestoreHelper;
    private DomainEventCollectorInterface $domainEventCollector;

    public function __construct(
        TrashItemRepositoryInterface   $trashItemRepository,
        EntityManagerInterface         $entityManager,
        DoctrineRestoreHelperInterface $doctrineRestoreHelper,
        DomainEventCollectorInterface  $domainEventCollector
    )
    {
        $this->trashItemRepository = $trashItemRepository;
        $this->entityManager = $entityManager;
        $this->doctrineRestoreHelper = $doctrineRestoreHelper;
        $this->domainEventCollector = $domainEventCollector;
    }

    public static function getResourceKey(): string
    {
        return Bulletin::RESOURCE_KEY;
    }

    public function store(object $resource, array $options = []): TrashItemInterface
    {
        $document = $resource->getDocument();
        $cover = $resource->getCover();

        $data = [
            "title" => $resource->getTitle(),
            "description" => $resource->getDescription(),
            "dateBulletin" => $resource->getDateBulletin(),
            "documentId" => $document ? $document->getId() : null,
            "coverId" => $cover ? $cover->getId() : null,
            "state" => $resource->getState(),
        ];

        return $this->trashItemRepository->create(
            Bulletin::RESOURCE_KEY,
            (string)$resource->getId(),
            $resource->getTitle(),
            $data,
            null,
            $options,
            Bulletin::SECURITY_CONTEXT,
            null,
            null
        );
    }

    public function restore(TrashItemInterface $trashItem, array $restoreFormData = []): object
    {
        $data = $trashItem->getRestoreData();
        $bulletinId = (int)$trashItem->getResourceId();
        $bulletin = new Bulletin();
        $bulletin->setTitle($data['title']);
        $bulletin->setDescription($data['description']);
        $bulletin->setDateBulletin(new \DateTimeImmutable($data['dateBulletin']['date']));
        if ($data['documentId']) {
            $bulletin->setDocument($this->entityManager->find(MediaInterface::class, $data['documentId']));
        }
        if ($data['coverId']) {
            $bulletin->setCover($this->entityManager->find(MediaInterface::class, $data['coverId']));
        }
        $bulletin->setState($data['state']);
        $this->domainEventCollector->collect(
            new BulletinRestoredEvent($bulletin, $data)
        );

        $this->doctrineRestoreHelper->persistAndFlushWithId($bulletin, $bulletinId);
        return $bulletin;
    }

    public function getConfiguration(): RestoreConfiguration
    {
        return new RestoreConfiguration(null, BulletinAdmin::EDIT_FORM_VIEW, ['id' => 'id']);
    }
}
