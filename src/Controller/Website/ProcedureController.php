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
    /**
     * @return array<mixed>
     */
    public static function getSubscribedServices()
    {
        $subscribedServices = parent::getSubscribedServices();

        $subscribedServices['sulu_core.webspace.webspace_manager'] = WebspaceManagerInterface::class;
        $subscribedServices['sulu.repository.route'] = RouteRepositoryInterface::class;
        $subscribedServices['sulu_website.resolver.template_attribute'] = TemplateAttributeResolverInterface::class;

        return $subscribedServices;
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
        $parameters = $this->container->get('sulu_website.resolver.template_attribute')->resolve([
            'procedure' => $procedure,
            'localizations' => $this->getLocalizationsArrayForEntity($procedure),
        ]);
        if ($partial) {
            $content = $this->renderBlock(
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
        $routes = $this->container->get('sulu.repository.route')->findAllByEntity(Procedure::class, (string) $entity->getId());

        $localizations = [];
        foreach ($routes as $route) {
            $url = $this->container->get('sulu_core.webspace.webspace_manager')->findUrlByResourceLocator(
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
     * Returns rendered part of template specified by block.
     *
     * @param mixed $template
     * @param mixed $block
     * @param mixed $attributes
     */
    protected function renderBlock($template, $block, $attributes = []): string
    {
        $twig = $this->container->get('twig');
        $attributes = $twig->mergeGlobals($attributes);

        $template = $twig->load($template);

        $level = ob_get_level();
        ob_start();

        try {
            $rendered = $template->renderBlock($block, $attributes);
            ob_end_clean();

            return $rendered;
        } catch (\Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $e;
        }
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
