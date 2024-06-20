<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Controller\Website;

use Pixel\TownHallBundle\Entity\Procedure;
use Sulu\Bundle\PreviewBundle\Preview\Preview;
use Sulu\Bundle\RouteBundle\Entity\RouteRepositoryInterface;
use Sulu\Bundle\WebsiteBundle\Resolver\TemplateAttributeResolverInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ProcedureController extends AbstractController
{
    private TemplateAttributeResolverInterface $templateAttributeResolver;

    private RouteRepositoryInterface $routeRepository;

    private WebspaceManagerInterface $webspaceManager;

    public function __construct(TemplateAttributeResolverInterface $templateAttributeResolver, RouteRepositoryInterface $routeRepository, WebspaceManagerInterface $webspaceManager)
    {
        $this->templateAttributeResolver = $templateAttributeResolver;
        $this->routeRepository = $routeRepository;
        $this->webspaceManager = $webspaceManager;
    }

    /**
     * @param array<mixed> $attributes
     * @throws \Exception
     */
    public function indexAction(Procedure $procedure, array $attributes = [], bool $preview = false, bool $partial = false): Response
    {
        if (! $procedure->getSeo() || (isset($procedure->getSeo()['title']) && ! $procedure->getSeo()['title'])) {
            $seo = [
                "title" => $procedure->getTitle(),
            ];

            $procedure->setSeo($seo);
        }
        $parameters = $this->templateAttributeResolver->resolve([
            'procedure' => $procedure,
            'localizations' => $this->getLocalizationsArrayForEntity($procedure),
        ]);
        if ($partial) {
            return $this->renderBlock(
                '@TownHall/procedure.html.twig',
                'content',
                $parameters
            );
        } elseif ($preview) {
            $content = $this->renderPreview(
                '@TownHall/procedure.html.twig',
                $parameters
            );
        } else {
            if (! $procedure->getState()) {
                throw $this->createNotFoundException();
            }
            $content = $this->renderView(
                '@TownHall/procedure.html.twig',
                $parameters
            );
        }

        return new Response($content);
    }

    /**
     * @return array<string, array<mixed>>
     */
    protected function getLocalizationsArrayForEntity(Procedure $entity): array
    {
        $routes = $this->routeRepository->findAllByEntity(Procedure::class, (string) $entity->getId());

        $localizations = [];
        foreach ($routes as $route) {
            $url = $this->webspaceManager->findUrlByResourceLocator(
                $route->getPath(),
                null,
                $route->getLocale()
            );

            $localizations[$route->getLocale()] = [
                'locale' => $route->getLocale(),
                'url' => $url,
            ];
        }

        return $localizations;
    }

    /**
     * @param array<string> $parameters
     */
    protected function renderPreview(string $view, array $parameters = []): string
    {
        $parameters['previewParentTemplate'] = $view;
        $parameters['previewContentReplacer'] = Preview::CONTENT_REPLACER;

        return $this->renderView('@SuluWebsite/Preview/preview.html.twig', $parameters);
    }
}
