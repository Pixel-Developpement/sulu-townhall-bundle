<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Trash;

use Doctrine\ORM\EntityManagerInterface;
use Pixel\TownHallBundle\Admin\ReportAdmin;
use Pixel\TownHallBundle\Domain\Event\ReportRestoreEvent;
use Pixel\TownHallBundle\Entity\Report;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Bundle\TrashBundle\Application\DoctrineRestoreHelper\DoctrineRestoreHelperInterface;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfiguration;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfigurationProviderInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\RestoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\StoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Domain\Model\TrashItemInterface;
use Sulu\Bundle\TrashBundle\Domain\Repository\TrashItemRepositoryInterface;

class ReportTrashItemHandler implements StoreTrashItemHandlerInterface, RestoreTrashItemHandlerInterface, RestoreConfigurationProviderInterface
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
        return Report::RESOURCE_KEY;
    }

    public function store(object $resource, array $options = []): TrashItemInterface
    {
        $document = $resource->getDocument();

        $data = [
            "title" => $resource->getTitle(),
            "dateReport" => $resource->getDateReport(),
            "documentId" => $document ? $document->getId() : null,
            "description" => $resource->getDescription(),
            "isActive" => $resource->getIsActive(),
        ];

        return $this->trashItemRepository->create(
            Report::RESOURCE_KEY,
            (string)$resource->getId(),
            $resource->getTitle(),
            $data,
            null,
            $options,
            Report::SECURITY_CONTEXT,
            null,
            null
        );
    }

    public function restore(TrashItemInterface $trashItem, array $restoreFormData = []): object
    {
        $data = $trashItem->getRestoreData();
        $reportId = (int)$trashItem->getResourceId();
        dump($data['dateReport']);
        $report = new Report();
        $report->setTitle($data['title']);
        $report->setDateReport(new \DateTimeImmutable($data['dateReport']['date']));
        if ($data['documentId']) {
            $report->setDocument($this->entityManager->find(MediaInterface::class, $data['documentId']));
        }
        $report->setDescription($data['description']);
        $report->setIsActive($data['isActive']);
        $this->domainEventCollector->collect(
            new ReportRestoreEvent($report, $data)
        );

        $this->doctrineRestoreHelper->persistAndFlushWithId($report, $reportId);
        return $report;
    }

    public function getConfiguration(): RestoreConfiguration
    {
        return new RestoreConfiguration(null, ReportAdmin::EDIT_FORM_VIEW, ['id' => 'id']);
    }
}
