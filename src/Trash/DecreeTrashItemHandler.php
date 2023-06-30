<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Trash;

use Doctrine\ORM\EntityManagerInterface;
use Pixel\TownHallBundle\Admin\DecreeAdmin;
use Pixel\TownHallBundle\Domain\Event\DecreeRestoredEvent;
use Pixel\TownHallBundle\Entity\Decree;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\CategoryBundle\Entity\CategoryInterface;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Bundle\TrashBundle\Application\DoctrineRestoreHelper\DoctrineRestoreHelperInterface;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfiguration;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfigurationProviderInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\RestoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\StoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Domain\Model\TrashItemInterface;
use Sulu\Bundle\TrashBundle\Domain\Repository\TrashItemRepositoryInterface;

class DecreeTrashItemHandler implements StoreTrashItemHandlerInterface, RestoreTrashItemHandlerInterface, RestoreConfigurationProviderInterface
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
        return Decree::RESOURCE_KEY;
    }

    public function store(object $resource, array $options = []): TrashItemInterface
    {
        $pdf = $resource->getPdf();
        $type = $resource->getCategory();

        $data = [
            "title" => $resource->getTitle(),
            "startDate" => $resource->getStartDate(),
            "endDate" => $resource->getEndDate(),
            "pdfId" => $pdf->getId(),
            "typeId" => $type->getId(),
            "description" => $resource->getDescription(),
            "isActive" => $resource->isActive(),
        ];

        return $this->trashItemRepository->create(
            Decree::RESOURCE_KEY,
            (string) $resource->getId(),
            $resource->getTitle(),
            $data,
            null,
            $options,
            Decree::SECURITY_CONTEXT,
            null,
            null
        );
    }

    public function restore(TrashItemInterface $trashItem, array $restoreFormData = []): object
    {
        $data = $trashItem->getRestoreData();
        $decreeId = (int) $trashItem->getResourceId();

        $decree = new Decree();
        $decree->setTitle($data['title']);
        $decree->setStartDate(new \DateTimeImmutable($data['startDate']['date']));
        if (isset($data['endDate'])) {
            $decree->setEndDate(new \DateTimeImmutable($data['endDate']['date']));
        }
        $decree->setPdf($this->entityManager->find(MediaInterface::class, $data['pdfId']));
        $decree->setCategory($this->entityManager->find(CategoryInterface::class, $data['typeId']));
        $decree->setDescription($data['description']);
        $decree->setIsActive($data['isActive']);
        $this->domainEventCollector->collect(
            new DecreeRestoredEvent($decree, $data)
        );

        $this->doctrineRestoreHelper->persistAndFlushWithId($decree, $decreeId);
        return $decree;
    }

    public function getConfiguration(): RestoreConfiguration
    {
        return new RestoreConfiguration(null, DecreeAdmin::EDIT_FORM_VIEW, [
            'id' => 'id',
        ]);
    }
}
