<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Trash;

use Doctrine\ORM\EntityManagerInterface;
use Pixel\TownHallBundle\Admin\JobOfferAdmin;
use Pixel\TownHallBundle\Domain\Event\JobOfferRestoredEvent;
use Pixel\TownHallBundle\Entity\JobOffer;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Bundle\TrashBundle\Application\DoctrineRestoreHelper\DoctrineRestoreHelperInterface;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfiguration;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfigurationProviderInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\RestoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\StoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Domain\Model\TrashItemInterface;
use Sulu\Bundle\TrashBundle\Domain\Repository\TrashItemRepositoryInterface;

class JobOfferTrashItemHandler implements StoreTrashItemHandlerInterface, RestoreTrashItemHandlerInterface, RestoreConfigurationProviderInterface
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
        return JobOffer::RESOURCE_KEY;
    }

    public function store(object $resource, array $options = []): TrashItemInterface
    {
        $pdf = $resource->getPdf();

        $data = [
            'name' => $resource->getName(),
            'description' => $resource->getDescription(),
            'contractType' => $resource->getContractType(),
            'duration' => $resource->getDuration(),
            'pdfId' => $pdf ? $pdf->getId() : null,
            'isActive' => $resource->getIsActive(),
            'publishedAt' => $resource->getPublishedAt(),
        ];

        return $this->trashItemRepository->create(
            JobOffer::RESOURCE_KEY,
            (string) $resource->getId(),
            $resource->getName(),
            $data,
            null,
            $options,
            JobOffer::SECURITY_CONTEXT,
            null,
            null
        );
    }

    public function restore(TrashItemInterface $trashItem, array $restoreFormData = []): object
    {
        $data = $trashItem->getRestoreData();
        $jobOfferId = (int) $trashItem->getResourceId();

        $jobOffer = new JobOffer();
        $jobOffer->setName($data['name']);
        $jobOffer->setDescription($data['description']);
        $jobOffer->setContractType($data['contractType']);
        if (isset($data['duration'])) {
            $jobOffer->setDuration($data['duration']);
        }
        if (isset($data['pdfId'])) {
            $jobOffer->setPdf($this->entityManager->find(MediaInterface::class, $data['pdfId']));
        }
        $jobOffer->setIsActive($data['isActive']);
        $jobOffer->setPublishedAt(new \DateTimeImmutable($data['publishedAt']['date']));
        $this->domainEventCollector->collect(
            new JobOfferRestoredEvent($jobOffer, $data)
        );

        $this->doctrineRestoreHelper->persistAndFlushWithId($jobOffer, $jobOfferId);
        return $jobOffer;
    }

    public function getConfiguration(): RestoreConfiguration
    {
        return new RestoreConfiguration(null, JobOfferAdmin::EDIT_FORM_VIEW, [
            'id' => "id",
        ]);
    }
}
