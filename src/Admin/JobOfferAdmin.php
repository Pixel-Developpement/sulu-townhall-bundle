<?php

declare(strict_types=1);

namespace Pixel\TownHallBundle\Admin;

use Pixel\TownHallBundle\Entity\JobOffer;
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

class JobOfferAdmin extends Admin
{
    public const LIST_VIEW = "townhall.job_offer.list";

    public const ADD_FORM_VIEW = "townhall.job_offer.add_form";

    public const ADD_FORM_VIEW_DETAILS = "townhall.job_offer.add_form_details";

    public const EDIT_FORM_VIEW = "townhall.job_offer.edit_form";

    public const EDIT_FORM_VIEW_DETAILS = "townhall.job_offer.edit_form_details";

    private ViewBuilderFactoryInterface $viewBuilderFactory;

    private SecurityCheckerInterface $securityChecker;

    private ActivityViewBuilderFactoryInterface $activityViewBuilderFactory;

    public function __construct(
        ViewBuilderFactoryInterface $viewBuilderFactory,
        SecurityCheckerInterface $securityChecker,
        ActivityViewBuilderFactoryInterface $activityViewBuilderFactory
    ) {
        $this->viewBuilderFactory = $viewBuilderFactory;
        $this->securityChecker = $securityChecker;
        $this->activityViewBuilderFactory = $activityViewBuilderFactory;
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        if ($this->securityChecker->hasPermission(JobOffer::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $navigationItem = new NavigationItem("townhall.job_offers");
            $navigationItem->setView(static::LIST_VIEW);
            $navigationItemCollection->get("townhall")->addChild($navigationItem);
        }
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $listToolbarActions = [];
        $formToolbarActions = [];

        if ($this->securityChecker->hasPermission(JobOffer::SECURITY_CONTEXT, PermissionTypes::ADD)) {
            $listToolbarActions[] = new ToolbarAction('sulu_admin.add');
        }

        if ($this->securityChecker->hasPermission(JobOffer::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $formToolbarActions[] = new ToolbarAction("sulu_admin.save");
            $formToolbarActions[] = new TogglerToolbarAction(
                "townhall.isActive",
                "isActive",
                "enable",
                "disable"
            );
        }

        if ($this->securityChecker->hasPermission(JobOffer::SECURITY_CONTEXT, PermissionTypes::DELETE)) {
            $listToolbarActions[] = new ToolbarAction("sulu_admin.delete");
            $formToolbarActions[] = new ToolbarAction("sulu_admin.delete");
        }

        if ($this->securityChecker->hasPermission(JobOffer::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            $listToolbarActions[] = new ToolbarAction("sulu_admin.export");
        }

        if ($this->securityChecker->hasPermission(JobOffer::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $viewCollection->add(
                $this->viewBuilderFactory->createListViewBuilder(static::LIST_VIEW, "/job_offers")
                ->setResourceKey(JobOffer::RESOURCE_KEY)
                ->setListKey(JobOffer::LIST_KEY)
                ->setTitle("townhall.job_offers")
                ->addListAdapters(['table'])
                ->setAddView(static::ADD_FORM_VIEW)
                ->setEditView(static::EDIT_FORM_VIEW)
                ->addToolbarActions($listToolbarActions)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createResourceTabViewBuilder(static::ADD_FORM_VIEW, "/jobOffers/add")
                ->setResourceKey(JobOffer::RESOURCE_KEY)
                ->setBackView(static::LIST_VIEW)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createFormViewBuilder(static::ADD_FORM_VIEW_DETAILS, "/details")
                ->setResourceKey(JobOffer::RESOURCE_KEY)
                ->setFormKey(JobOffer::FORM_KEY)
                ->setTabTitle("sulu_admin.details")
                ->setEditView(static::EDIT_FORM_VIEW)
                ->addToolbarActions($formToolbarActions)
                ->setParent(static::ADD_FORM_VIEW)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createResourceTabViewBuilder(static::EDIT_FORM_VIEW, "/jobOffers/:id")
                    ->setResourceKey(JobOffer::RESOURCE_KEY)
                    ->setBackView(static::LIST_VIEW)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createFormViewBuilder(static::EDIT_FORM_VIEW_DETAILS, "/details")
                    ->setResourceKey(JobOffer::RESOURCE_KEY)
                    ->setFormKey(JobOffer::FORM_KEY)
                    ->setTabTitle("sulu_admin.details")
                    ->addToolbarActions($formToolbarActions)
                    ->setParent(static::EDIT_FORM_VIEW)
            );

            if ($this->activityViewBuilderFactory->hasActivityListPermission()) {
                $viewCollection->add(
                    $this->activityViewBuilderFactory->createActivityListViewBuilder(static::EDIT_FORM_VIEW . ".activity", "/activity", JobOffer::RESOURCE_KEY)
                    ->setParent(static::EDIT_FORM_VIEW)
                );
            }
        }
    }

    public function getSecurityContexts()
    {
        return [
            self::SULU_ADMIN_SECURITY_SYSTEM => [
                'JobOffer' => [
                    JobOffer::SECURITY_CONTEXT => [
                        PermissionTypes::VIEW,
                        PermissionTypes::DELETE,
                        PermissionTypes::ADD,
                        PermissionTypes::EDIT
                    ],
                ],
            ],
        ];
    }
}
