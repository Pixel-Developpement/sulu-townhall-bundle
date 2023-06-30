<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Trash;

use Doctrine\ORM\EntityManagerInterface;
use Pixel\TownHallBundle\Admin\FlashInfoAdmin;
use Pixel\TownHallBundle\Domain\Event\FlashInfoRestoredEvent;
use Pixel\TownHallBundle\Entity\FlashInfo;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Bundle\TrashBundle\Application\DoctrineRestoreHelper\DoctrineRestoreHelperInterface;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfiguration;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfigurationProviderInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\RestoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\StoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Domain\Model\TrashItemInterface;
use Sulu\Bundle\TrashBundle\Domain\Repository\TrashItemRepositoryInterface;

class FlashInfoTrashItemHandler implements StoreTrashItemHandlerInterface, RestoreTrashItemHandlerInterface, RestoreConfigurationProviderInterface
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
        return FlashInfo::RESOURCE_KEY;
    }

    public function store(object $resource, array $options = []): TrashItemInterface
    {
        $cover = $resource->getCover();

        $data = [
            "title" => $resource->getTitle(),
            "description" => $resource->getDescription(),
            "coverId" => $cover ? $cover->getId() : null,
            "documents" => $resource->getPdfs(),
            "isActive" => $resource->getIsActive(),
            "publishedAt" => $resource->getPublishedAt(),
        ];

        return $this->trashItemRepository->create(
            FlashInfo::RESOURCE_KEY,
            (string) $resource->getId(),
            $resource->getTitle(),
            $data,
            null,
            $options,
            FlashInfo::SECURITY_CONTEXT,
            null,
            null
        );
    }

    public function restore(TrashItemInterface $trashItem, array $restoreFormData = []): object
    {
        $data = $trashItem->getRestoreData();
        $flashInfoId = (int) $trashItem->getResourceId();
        $flashInfo = new FlashInfo();
        $flashInfo->setTitle($data['title']);
        $flashInfo->setDescription($data['description']);
        $flashInfo->setCover($this->entityManager->find(MediaInterface::class, $data['coverId']));
        if (isset($data['documents'])) {
            $flashInfo->setPdfs($data['documents']);
        }
        $flashInfo->setIsActive($data['isActive']);
        /*if(isset($data['publishedAt'])){
            $flashInfo->setPublishedAt(new \DateTimeImmutable($data['publishedAt']['date']));
        }*/
        $this->domainEventCollector->collect(
            new FlashInfoRestoredEvent($flashInfo, $data)
        );

        $this->doctrineRestoreHelper->persistAndFlushWithId($flashInfo, $flashInfoId);
        return $flashInfo;
    }

    public function getConfiguration(): RestoreConfiguration
    {
        return new RestoreConfiguration(null, FlashInfoAdmin::EDIT_FORM_VIEW, [
            'id' => 'id',
        ]);
    }
}
