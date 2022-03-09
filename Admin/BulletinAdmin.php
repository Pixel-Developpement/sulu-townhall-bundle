<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Admin;

use Pixel\TownHallBundle\Entity\Bulletin;
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

class BulletinAdmin extends Admin
{
    public const LIST_VIEW = 'townhall.bulletin.list';
    public const ADD_FORM_VIEW = 'townhall.bulletin.add_form';
    public const ADD_FORM_DETAILS_VIEW = 'townhall.bulletin.add_form.details';
    public const EDIT_FORM_VIEW = 'townhall.bulletin.edit_form';
    public const EDIT_FORM_DETAILS_VIEW = 'townhall.bulletin.edit_form.details';

    private ViewBuilderFactoryInterface $viewBuilderFactory;
    private SecurityCheckerInterface $securityChecker;
    private ActivityViewBuilderFactoryInterface $activityViewBuilderFactory;

    public function __construct(
        ViewBuilderFactoryInterface         $viewBuilderFactory,
        SecurityCheckerInterface            $securityChecker,
        ActivityViewBuilderFactoryInterface $activityViewBuilderFactory
    )
    {
        $this->viewBuilderFactory = $viewBuilderFactory;
        $this->securityChecker = $securityChecker;
        $this->activityViewBuilderFactory = $activityViewBuilderFactory;
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        if ($this->securityChecker->hasPermission(Bulletin::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $navigationItem = new NavigationItem('townhall.bulletins');
            $navigationItem->setView(static::LIST_VIEW);
            $navigationItemCollection->get('townhall')->addChild($navigationItem);
        }
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $formToolbarActions = [];
        $listToolbarActions = [];

        if ($this->securityChecker->hasPermission(Bulletin::SECURITY_CONTEXT, PermissionTypes::ADD)) {
            $listToolbarActions[] = new ToolbarAction('sulu_admin.add');
        }

        if ($this->securityChecker->hasPermission(Bulletin::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $formToolbarActions[] = new ToolbarAction('sulu_admin.save');
            $formToolbarActions[] = new TogglerToolbarAction('townhall.state',
                'state',
                'enable',
                'disable');
        }

        if ($this->securityChecker->hasPermission(Bulletin::SECURITY_CONTEXT, PermissionTypes::DELETE)) {
            $formToolbarActions[] = new ToolbarAction('sulu_admin.delete');
            $listToolbarActions[] = new ToolbarAction('sulu_admin.delete');
        }

        if ($this->securityChecker->hasPermission(Bulletin::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            $listToolbarActions[] = new ToolbarAction('sulu_admin.export');
        }

        if ($this->securityChecker->hasPermission(Bulletin::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $viewCollection->add(
                $this->viewBuilderFactory->createListViewBuilder(static::LIST_VIEW, '/bulletins')
                    ->setResourceKey(Bulletin::RESOURCE_KEY)
                    ->setListKey(Bulletin::LIST_KEY)
                    ->setTitle('townhall.bulletins')
                    ->addListAdapters(['table'])
                    ->setAddView(static::ADD_FORM_VIEW)
                    ->setEditView(static::EDIT_FORM_VIEW)
                    ->addToolbarActions($listToolbarActions)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createResourceTabViewBuilder(static::ADD_FORM_VIEW, '/bulletins/add')
                    ->setResourceKey(Bulletin::RESOURCE_KEY)
                    ->setBackView(static::LIST_VIEW)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createFormViewBuilder(static::ADD_FORM_DETAILS_VIEW, '/details')
                    ->setResourceKey(Bulletin::RESOURCE_KEY)
                    ->setFormKey(Bulletin::FORM_KEY)
                    ->setTabTitle('sulu_admin.details')
                    ->setEditView(static::EDIT_FORM_VIEW)
                    ->addToolbarActions($formToolbarActions)
                    ->setParent(static::ADD_FORM_VIEW)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createResourceTabViewBuilder(static::EDIT_FORM_VIEW, '/bulletins/:id')
                    ->setResourceKey(Bulletin::RESOURCE_KEY)
                    ->setBackView(static::LIST_VIEW)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createFormViewBuilder(static::EDIT_FORM_DETAILS_VIEW, '/details')
                    ->setResourceKey(Bulletin::RESOURCE_KEY)
                    ->setFormKey(Bulletin::FORM_KEY)
                    ->setTabTitle('sulu_admin.details')
                    ->addToolbarActions($formToolbarActions)
                    ->setParent(static::EDIT_FORM_VIEW)
            );

            if ($this->activityViewBuilderFactory->hasActivityListPermission()) {
                $viewCollection->add(
                    $this->activityViewBuilderFactory->createActivityListViewBuilder(static::EDIT_FORM_VIEW . ".activity", "/activity", Bulletin::RESOURCE_KEY)
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
                'Bulletin' => [
                    Bulletin::SECURITY_CONTEXT => [
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
