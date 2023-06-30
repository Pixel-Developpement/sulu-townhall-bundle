<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Preview;

use Pixel\TownHallBundle\Entity\Procedure;
use Pixel\TownHallBundle\Repository\ProcedureRepository;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Bundle\PreviewBundle\Preview\Object\PreviewObjectProviderInterface;

class ProcedureObjectProvider implements PreviewObjectProviderInterface
{
    private ProcedureRepository $procedureRepository;

    private MediaManagerInterface $mediaManager;

    public function __construct(ProcedureRepository $procedureRepository, MediaManagerInterface $mediaManager)
    {
        $this->procedureRepository = $procedureRepository;
        $this->mediaManager = $mediaManager;
    }

    public function getObject($id, $locale): Procedure
    {
        return $this->procedureRepository->find((int) $id);
    }

    /**
     * @param Procedure $object
     */
    public function getId($object): string
    {
        return (string) $object->getId();
    }

    /**
     * @param Procedure $object
     * @param array<mixed> $data
     */
    public function setValues($object, $locale, array $data): void
    {
        $state = $data['state'] ?? null;
        $coverId = $data['cover']['id'] ?? null;
        $documentId = $data['document']['id'] ?? null;
        $externalLink = $data['externalLink'] ?? null;

        $object->setTitle($data['title']);
        $object->setState($state);
        $object->setRoutePath($data['routePath']);
        $object->setCover($coverId ? $this->mediaManager->getEntityById($coverId) : null);
        $object->setDocument($documentId ? $this->mediaManager->getEntityById($documentId) : null);
        $object->setExternalLink($externalLink);
        $object->setDescription($data['description']);
    }

    /**
     * @param object $object
     * @param string $locale
     * @param array<mixed> $context
     * @return mixed
     */
    public function setContext($object, $locale, array $context)
    {
        if (\array_key_exists('template', $context)) {
            $object->setStructureType($context['template']);
        }

        return $object;
    }

    /**
     * @param Procedure $object
     * @return string
     */
    public function serialize($object)
    {
        if (! $object->getTitle()) {
            $object->setTitle('');
        }
        if (! $object->getDescription()) {
            $object->setDescription('');
        }
        //if (!$object->getMedias()) $object->setMedias('');

        return serialize($object);
    }

    /**
     * @return mixed
     */
    public function deserialize($serializedObject, $objectClass)
    {
        return unserialize($serializedObject);
    }

    public function getSecurityContext($id, $locale): ?string
    {
        return Procedure::SECURITY_CONTEXT;
    }
}
