<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Trash;

use Doctrine\ORM\EntityManagerInterface;
use Pixel\TownHallBundle\Admin\DeliberationAdmin;
use Pixel\TownHallBundle\Domain\Event\DeliberationRestoredEvent;
use Pixel\TownHallBundle\Entity\Deliberation;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Bundle\TrashBundle\Application\DoctrineRestoreHelper\DoctrineRestoreHelperInterface;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfiguration;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfigurationProviderInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\RestoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\StoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Domain\Model\TrashItemInterface;
use Sulu\Bundle\TrashBundle\Domain\Repository\TrashItemRepositoryInterface;

class DeliberationTrashItemHandler implements StoreTrashItemHandlerInterface, RestoreTrashItemHandlerInterface, RestoreConfigurationProviderInterface
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
        return Deliberation::RESOURCE_KEY;
    }

    public function store(object $resource, array $options = []): TrashItemInterface
    {
        $pdf = $resource->getPdf();

        $data = [
            'title' => $resource->getTitle(),
            'date' => $resource->getDate(),
            'pdfId' => $pdf->getId(),
            'description' => $resource->getDescription(),
            'isActive' => $resource->isActive(),
        ];

        return $this->trashItemRepository->create(
            Deliberation::RESOURCE_KEY,
            (string) $resource->getId(),
            $resource->getTitle(),
            $data,
            null,
            $options,
            Deliberation::SECURITY_CONTEXT,
            null,
            null
        );
    }

    public function restore(TrashItemInterface $trashItem, array $restoreFormData = []): object
    {
        $data = $trashItem->getRestoreData();
        $deliberationId = (int) $trashItem->getResourceId();

        $deliberation = new Deliberation();
        $deliberation->setTitle($data['title']);
        $deliberation->setDate(new \DateTimeImmutable($data['date']['date']));
        $deliberation->setPdf($this->entityManager->find(MediaInterface::class, $data['pdfId']));
        if (isset($data['description'])) {
            $deliberation->setDescription($data['description']);
        }
        $deliberation->setIsActive($data['isActive']);
        $this->domainEventCollector->collect(
            new DeliberationRestoredEvent($deliberation, $data)
        );

        $this->doctrineRestoreHelper->persistAndFlushWithId($deliberation, $deliberationId);
        return $deliberation;
    }

    public function getConfiguration(): RestoreConfiguration
    {
        return new RestoreConfiguration(null, DeliberationAdmin::EDIT_FORM_VIEW, [
            'id' => "id",
        ]);
    }
}
