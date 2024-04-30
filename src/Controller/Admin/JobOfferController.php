<?php

namespace Pixel\TownHallBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandlerInterface;
use HandcraftedInTheAlps\RestRoutingBundle\Controller\Annotations\RouteResource;
use HandcraftedInTheAlps\RestRoutingBundle\Routing\ClassResourceInterface;
use Pixel\TownHallBundle\Common\DoctrineListRepresentationFactory;
use Pixel\TownHallBundle\Domain\Event\JobOfferCreatedEvent;
use Pixel\TownHallBundle\Domain\Event\JobOfferModifiedEvent;
use Pixel\TownHallBundle\Domain\Event\JobOfferRemovedEvent;
use Pixel\TownHallBundle\Entity\JobOffer;
use Pixel\TownHallBundle\Repository\JobOfferRepository;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Bundle\RouteBundle\Entity\RouteRepositoryInterface;
use Sulu\Bundle\RouteBundle\Manager\RouteManagerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashManager\TrashManagerInterface;
use Sulu\Component\Rest\AbstractRestController;
use Sulu\Component\Rest\Exception\RestException;
use Sulu\Component\Rest\RequestParametersTrait;
use Sulu\Component\Security\SecuredControllerInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @RouteResource("job-offer")
 */
class JobOfferController extends AbstractRestController implements ClassResourceInterface, SecuredControllerInterface
{
    use RequestParametersTrait;

    private DoctrineListRepresentationFactory $doctrineListRepresentationFactory;

    private EntityManagerInterface $entityManager;

    private JobOfferRepository $jobOfferRepository;

    private MediaManagerInterface $mediaManager;

    private TrashManagerInterface $trashManager;

    private DomainEventCollectorInterface $domainEventCollector;

    public function __construct(
        DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
        EntityManagerInterface $entityManager,
        JobOfferRepository $jobOfferRepository,
        MediaManagerInterface $mediaManager,
        TrashManagerInterface $trashManager,
        DomainEventCollectorInterface $domainEventCollector,
        ViewHandlerInterface $viewHandler,
        ?TokenStorageInterface $tokenStorage
    ) {
        $this->doctrineListRepresentationFactory = $doctrineListRepresentationFactory;
        $this->entityManager = $entityManager;
        $this->jobOfferRepository = $jobOfferRepository;
        $this->mediaManager = $mediaManager;
        $this->trashManager = $trashManager;
        $this->domainEventCollector = $domainEventCollector;
        parent::__construct($viewHandler, $tokenStorage);
    }

    public function cgetAction(): Response
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            JobOffer::RESOURCE_KEY
        );

        return $this->handleView($this->view($listRepresentation));
    }

    public function getAction(int $id): Response
    {
        $jobOffer = $this->jobOfferRepository->findById($id);
        if (! $jobOffer) {
            throw new NotFoundHttpException();
        }

        return $this->handleView($this->view($jobOffer));
    }

    public function putAction(Request $request, int $id): Response
    {
        $jobOffer = $this->jobOfferRepository->findById($id);
        if (! $jobOffer) {
            throw new NotFoundHttpException();
        }

        $data = $request->request->all();
        $this->mapDataToEntity($data, $jobOffer);
        $this->domainEventCollector->collect(
            new JobOfferModifiedEvent($jobOffer, $data)
        );
        $this->entityManager->persist($jobOffer);
        $this->entityManager->flush();

        return $this->handleView($this->view($jobOffer));
    }

    public function postAction(Request $request): Response
    {
        $jobOffer = new JobOffer();
        $data = $request->request->all();
        $this->mapDataToEntity($data, $jobOffer);
        $this->domainEventCollector->collect(
            new JobOfferCreatedEvent($jobOffer, $data)
        );
        $this->entityManager->persist($jobOffer);
        $this->entityManager->flush();

        return $this->handleView($this->view($jobOffer, 201));
    }

    public function deleteAction(int $id): Response
    {
        /** @var JobOffer $jobOffer */
        $jobOffer = $this->jobOfferRepository->findById($id);
        $jobOfferName = $jobOffer->getName();

        if ($jobOffer) {
            $this->trashManager->store(JobOffer::RESOURCE_KEY, $jobOffer);
            $this->entityManager->remove($jobOffer);
            $this->domainEventCollector->collect(
                new JobOfferRemovedEvent($id, $jobOfferName)
            );
        }
        $this->entityManager->flush();

        return $this->handleView($this->view(null, 204));
    }

    /**
     * @Rest\Post("/job-offers/{id}")
     */
    public function postTriggerAction(int $id, Request $request): Response
    {
        $action = $this->getRequestParameter($request, "action", true);

        try {
            switch ($action) {
                case "enable":
                    $item = $this->entityManager->getReference(JobOffer::class, $id);
                    $item->setIsActive(true);
                    $this->entityManager->persist($item);
                    $this->entityManager->flush();
                    break;
                case "disable":
                    $item = $this->entityManager->getReference(JobOffer::class, $id);
                    $item->setIsActive(false);
                    $this->entityManager->persist($item);
                    $this->entityManager->flush();
                    break;
                default:
                    throw new BadRequestHttpException(sprintf("Unknown action %s", $action));
            }
        } catch (RestException $e) {
            $view = $this->view($e->toArray(), 400);
            return $this->handleView($view);
        }

        return $this->handleView($this->view($item));
    }

    /**
     * @param array<mixed> $data
     */
    protected function mapDataToEntity(array $data, JobOffer $entity): void
    {
        $duration = $data['duration'] ?? null;
        $pdfId = $data['pdf']['id'] ?? null;
        $isActive = $data['isActive'] ?? null;

        $entity->setName($data['name']);
        $entity->setDescription($data['description']);
        $entity->setContractType($data['contractType']);
        $entity->setDuration($duration);
        $entity->setPdf($pdfId ? $this->mediaManager->getEntityById($pdfId) : null);
        $entity->setIsActive($isActive);
        $entity->setPublishedAt(new \DateTimeImmutable($data['publishedAt']));
    }

    public function getSecurityContext()
    {
        return JobOffer::SECURITY_CONTEXT;
    }
}
