<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Link;

use Pixel\TownHallBundle\Entity\Procedure;
use Pixel\TownHallBundle\Repository\ProcedureRepository;
use Sulu\Bundle\MarkupBundle\Markup\Link\LinkConfigurationBuilder;
use Sulu\Bundle\MarkupBundle\Markup\Link\LinkItem;
use Sulu\Bundle\MarkupBundle\Markup\Link\LinkProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProcedureLinkProvider implements LinkProviderInterface
{
    private ProcedureRepository $procedureRepository;
    private TranslatorInterface $translator;

    public function __construct(ProcedureRepository $procedureRepository, TranslatorInterface $translator)
    {
        $this->procedureRepository = $procedureRepository;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return LinkConfigurationBuilder::create()
            ->setTitle($this->translator->trans('townhall.procedure'))
            ->setResourceKey(Procedure::RESOURCE_KEY) // the resourceKey of the entity that should be loaded
            ->setListAdapter('column_list')
            ->setDisplayProperties(['title'])
            ->setOverlayTitle($this->translator->trans('townhall.procedure'))
            ->setEmptyText($this->translator->trans('townhall.procedure.emptyProcedure'))
            ->setIcon('fa-university')
            ->getLinkConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function preload(array $hrefs, $locale, $published = true): array
    {
        if (0 === count($hrefs)) {
            return [];
        }

        $items = $this->procedureRepository->findBy(['id' => $hrefs]); // load items by id
        foreach ($items as $item) {
            $result[] = new LinkItem($item->getId(), $item->getTitle(), $item->getRoutePath(), $item->getState()); // create link-item foreach item
        }

        return $result;
    }
}
