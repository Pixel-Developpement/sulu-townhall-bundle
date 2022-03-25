<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Automation;

use Doctrine\ORM\EntityManagerInterface;
use Pixel\TownHallBundle\Entity\FlashInfo;
use Sulu\Bundle\AutomationBundle\TaskHandler\AutomationTaskHandlerInterface;
use Sulu\Bundle\AutomationBundle\TaskHandler\TaskHandlerConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FlashInfoUnpublishTaskHandler implements AutomationTaskHandlerInterface
{
    private EntityManagerInterface $entityManager;
    private TranslatorInterface $translator;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    public function handle($workload)
    {
        if (!\is_array($workload)) {
            return;
        }
        $class = $workload['class'];
        $repository = $this->entityManager->getRepository($class);
        $entity = $repository->findOneBy(['id' => $workload['id']]);
        if ($entity === null) {
            return;
        }
        $entity->setIsActive(false);
        $this->entityManager->flush();
    }

    public function configureOptionsResolver(OptionsResolver $optionsResolver): OptionsResolver
    {
        return $optionsResolver->setRequired(['id', 'locale'])
            ->setAllowedTypes('id', 'string')
            ->setAllowedTypes('locale', 'string');
    }

    public function supports(string $entityClass): bool
    {
        return $entityClass === FlashInfo::class || \is_subclass_of($entityClass, FlashInfo::class);
    }

    public function getConfiguration(): TaskHandlerConfiguration
    {
        return TaskHandlerConfiguration::create($this->translator->trans("townhall.flash_info.unpublish"));
    }
}