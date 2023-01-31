<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Admin;

use Pixel\TownHallBundle\Entity\Procedure;
use Sulu\Bundle\ActivityBundle\Infrastructure\Sulu\Admin\View\ActivityViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\TogglerToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

class ProcedureAdmin extends Admin
{
    public const LIST_VIEW = 'townhall.procedure.list';

    public const ADD_FORM_VIEW = 'townhall.procedure.add_form';

    public const ADD_FORM_DETAILS_VIEW = 'townhall.procedure.add_form.details';

    public const EDIT_FORM_VIEW = 'townhall.procedure.edit_form';

    public const EDIT_FORM_DETAILS_VIEW = 'townhall.procedure.edit_form.details';

    public const EDIT_FORM_SEO_VIEW = 'townahall.procedure.seo.edit_form';

    private ViewBuilderFactoryInterface $viewBuilderFactory;

    private SecurityCheckerInterface $securityChecker;

    private WebspaceManagerInterface $webspaceManager;

    private ActivityViewBuilderFactoryInterface $activityViewBuilderFactory;

    public function __construct(
        ViewBuilderFactoryInterface $viewBuilderFactory,
        SecurityCheckerInterface $securityChecker,
        WebspaceManagerInterface $webspaceManager,
        ActivityViewBuilderFactoryInterface $activityViewBuilderFactory
    ) {
        $this->viewBuilderFactory = $viewBuilderFactory;
        $this->securityChecker = $securityChecker;
        $this->webspaceManager = $webspaceManager;
        $this->activityViewBuilderFactory = $activityViewBuilderFactory;
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        if ($this->securityChecker->hasPermission(Procedure::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $navigationItem = new NavigationItem('townhall.procedures');
            $navigationItem->setView(static::LIST_VIEW);
            $navigationItemCollection->get('townhall')->addChild($navigationItem);
        }
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $locales = $this->webspaceManager->getAllLocales();
        $formToolbarActions = [];
        $listToolbarActions = [];

        if ($this->securityChecker->hasPermission(Procedure::SECURITY_CONTEXT, PermissionTypes::ADD)) {
            $listToolbarActions[] = new ToolbarAction('sulu_admin.add');
        }

        if ($this->securityChecker->hasPermission(Procedure::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $formToolbarActions[] = new ToolbarAction('sulu_admin.save');
            $formToolbarActions[] = new TogglerToolbarAction(
                'townhall.state',
                'state',
                'enable',
                'disable'
            );
        }

        if ($this->securityChecker->hasPermission(Procedure::SECURITY_CONTEXT, PermissionTypes::DELETE)) {
            $formToolbarActions[] = new ToolbarAction('sulu_admin.delete');
            $listToolbarActions[] = new ToolbarAction('sulu_admin.delete');
        }

        if ($this->securityChecker->hasPermission(Procedure::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            $listToolbarActions[] = new ToolbarAction('sulu_admin.export');
        }

        if ($this->securityChecker->hasPermission(Procedure::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $viewCollection->add(
                $this->viewBuilderFactory->createListViewBuilder(static::LIST_VIEW, '/procedures/:locale')
                    ->setResourceKey(Procedure::RESOURCE_KEY)
                    ->setListKey(Procedure::LIST_KEY)
                    ->setTitle('townhall.procedures')
                    ->addListAdapters(['table'])
                    ->addLocales($locales)
                    ->setDefaultLocale($locales[0])
                    ->setAddView(static::ADD_FORM_VIEW)
                    ->setEditView(static::EDIT_FORM_VIEW)
                    ->addToolbarActions($listToolbarActions)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createResourceTabViewBuilder(static::ADD_FORM_VIEW, '/procedures/:locale/add')
                    ->setResourceKey(Procedure::RESOURCE_KEY)
                    ->addLocales($locales)
                    ->setBackView(static::LIST_VIEW)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createFormViewBuilder(static::ADD_FORM_DETAILS_VIEW, '/details')
                    ->setResourceKey(Procedure::RESOURCE_KEY)
                    ->setFormKey(Procedure::FORM_KEY)
                    ->setTabTitle('sulu_admin.details')
                    ->setEditView(static::EDIT_FORM_VIEW)
                    ->addToolbarActions($formToolbarActions)
                    ->setParent(static::ADD_FORM_VIEW)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createResourceTabViewBuilder(static::EDIT_FORM_VIEW, '/procedures/:locale/:id')
                    ->setResourceKey(Procedure::RESOURCE_KEY)
                    ->addLocales($locales)
                    ->setBackView(static::LIST_VIEW)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createPreviewFormViewBuilder(static::EDIT_FORM_DETAILS_VIEW, '/details')
                    ->setResourceKey(Procedure::RESOURCE_KEY)
                    ->setFormKey(Procedure::FORM_KEY)
                    ->setTabTitle('sulu_admin.details')
                    ->addToolbarActions($formToolbarActions)
                    ->setParent(static::EDIT_FORM_VIEW)
            );

            $viewCollection->add(
                $this->viewBuilderFactory
                    ->createFormViewBuilder(static::EDIT_FORM_SEO_VIEW, '/seo')
                    ->setResourceKey(Procedure::RESOURCE_KEY)
                    ->setFormKey('seo')
                    ->setTabTitle('sulu_page.seo')
                    ->addToolbarActions($formToolbarActions)
                    ->setTitleVisible(true)
                    ->setTabOrder(2048)
                    ->setParent(static::EDIT_FORM_VIEW)
            );

            if ($this->activityViewBuilderFactory->hasActivityListPermission()) {
                $viewCollection->add(
                    $this->activityViewBuilderFactory->createActivityListViewBuilder(static::EDIT_FORM_VIEW . ".activity", "/activity", Procedure::RESOURCE_KEY)
                        ->setParent(static::EDIT_FORM_VIEW)
                );
            }
        }
    }

    /**
     * @return mixed[]
     */
    public function getSecurityContexts(): array
    {
        return [
            self::SULU_ADMIN_SECURITY_SYSTEM => [
                'Procedure' => [
                    Procedure::SECURITY_CONTEXT => [
                        PermissionTypes::VIEW,
                        PermissionTypes::ADD,
                        PermissionTypes::EDIT,
                        PermissionTypes::DELETE,
                    ],
                ],
            ],
        ];
    }
}
