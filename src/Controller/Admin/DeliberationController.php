<?php

namespace Pixel\TownHallBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandlerInterface;
use HandcraftedInTheAlps\RestRoutingBundle\Controller\Annotations\RouteResource;
use HandcraftedInTheAlps\RestRoutingBundle\Routing\ClassResourceInterface;
use Pixel\TownHallBundle\Common\DoctrineListRepresentationFactory;
use Pixel\TownHallBundle\Domain\Event\DeliberationCreatedEvent;
use Pixel\TownHallBundle\Domain\Event\DeliberationModifiedEvent;
use Pixel\TownHallBundle\Domain\Event\DeliberationRemovedEvent;
use Pixel\TownHallBundle\Entity\Deliberation;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashManager\TrashManagerInterface;
use Sulu\Component\Rest\AbstractRestController;
use Sulu\Component\Rest\Exception\RestException;
use Sulu\Component\Rest\RequestParametersTrait;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @RouteResource("deliberation")
 */
class DeliberationController extends AbstractRestController implements ClassResourceInterface, SecuredControllerInterface
{
    use RequestParametersTrait;

    private DoctrineListRepresentationFactory $doctrineListRepresentationFactory;

    private EntityManagerInterface $entityManager;

    private MediaManagerInterface $mediaManager;

    private TrashManagerInterface $trashManager;

    private DomainEventCollectorInterface $domainEventCollector;

    public function __construct(
        DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
        EntityManagerInterface $entityManager,
        MediaManagerInterface $mediaManager,
        TrashManagerInterface $trashManager,
        DomainEventCollectorInterface $domainEventCollector,
        ViewHandlerInterface $viewHandler,
        ?TokenStorageInterface $tokenStorage = null
    ) {
        $this->doctrineListRepresentationFactory = $doctrineListRepresentationFactory;
        $this->entityManager = $entityManager;
        $this->mediaManager = $mediaManager;
        $this->trashManager = $trashManager;
        $this->domainEventCollector = $domainEventCollector;
        parent::__construct($viewHandler, $tokenStorage);
    }

    public function cgetAction(): Response
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            Deliberation::RESOURCE_KEY
        );
        return $this->handleView($this->view($listRepresentation));
    }

    public function getAction(int $id): Response
    {
        $deliberation = $this->entityManager->getRepository(Deliberation::class)->find($id);

        if (! $deliberation) {
            throw new NotFoundHttpException();
        }
        return $this->handleView($this->view($deliberation));
    }

    public function putAction(Request $request, int $id): Response
    {
        $deliberation = $this->entityManager->getRepository(Deliberation::class)->find($id);

        if (! $deliberation) {
            throw new NotFoundHttpException();
        }

        $data = $request->request->all();
        $this->mapDataToEntity($data, $deliberation);
        $this->domainEventCollector->collect(
            new DeliberationModifiedEvent($deliberation, $data)
        );
        $this->entityManager->flush();

        return $this->handleView($this->view($deliberation));
    }

    /**
     * @param array<mixed> $data
     */
    protected function mapDataToEntity(array $data, Deliberation $entity): void
    {
        $description = $data['description'] ?? null;
        $isActive = $data['isActive'] ?? null;

        $entity->setTitle($data['title']);
        $entity->setDate(new \DateTimeImmutable($data['date']));
        $entity->setPdf($this->mediaManager->getEntityById($data['pdf']['id']));
        $entity->setDescription($description);
        $entity->setIsActive($isActive);
    }

    public function postAction(Request $request): Response
    {
        $deliberation = new Deliberation();
        $data = $request->request->all();
        $this->mapDataToEntity($data, $deliberation);

        $this->entityManager->persist($deliberation);
        $this->domainEventCollector->collect(
            new DeliberationCreatedEvent($deliberation, $data)
        );
        $this->entityManager->flush();

        return $this->handleView($this->view($deliberation, 201));
    }

    public function deleteAction(int $id): Response
    {
        /** @var Deliberation $deliberation */
        $deliberation = $this->entityManager->getRepository(Deliberation::class)->find($id);
        $deliberationTitle = $deliberation->getTitle();
        if ($deliberation) {
            $this->trashManager->store(Deliberation::RESOURCE_KEY, $deliberation);
            $this->entityManager->remove($deliberation);
            $this->domainEventCollector->collect(
                new DeliberationRemovedEvent($id, $deliberationTitle)
            );
        }
        $this->entityManager->flush();

        return $this->handleView($this->view(null, 204));
    }

    public function getSecurityContext(): string
    {
        return Deliberation::SECURITY_CONTEXT;
    }

    /**
     * @Rest\Post("/deliberations/{id}")
     */
    public function postTriggerAction(int $id, Request $request): Response
    {
        $action = $this->getRequestParameter($request, "action", true);

        try {
            switch ($action) {
                case "enable":
                    $item = $this->entityManager->getReference(Deliberation::class, $id);
                    $item->setIsActive(true);
                    $this->entityManager->persist($item);
                    $this->entityManager->flush();
                    break;
                case "disable":
                    $item = $this->entityManager->getReference(Deliberation::class, $id);
                    $item->setIsActive(false);
                    $this->entityManager->persist($item);
                    $this->entityManager->flush();
                    break;
                default:
                    throw new BadRequestHttpException(sprintf('Unknown action "%s"', $action));
            }
        } catch (RestException $exception) {
            $view = $this->view($exception->toArray(), 400);
            return $this->handleView($view);
        }

        return $this->handleView($this->view($item));
    }
}
