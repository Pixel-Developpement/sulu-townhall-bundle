# Town Hall Bundle

![GitHub release (with filter)](https://img.shields.io/github/v/release/Pixel-Mairie/sulu-townhallbundle) [![Dependency](https://img.shields.io/badge/sulu-2.5-cca000.svg)](https://sulu.io/) [![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=Pixel-Mairie_sulu-townhallbundle&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=Pixel-Mairie_sulu-townhallbundle)

## Presentation
A Sulu bundle to manage town hall related entities.

## Features
* Flash info management
* Report management
* Bulletin management
* Procedure management
* Decree management
* Public market management
* Deliberation management
* List of entities (via smart content)
* Preview
* Translation
* Settings
* SEO
* Activity log
* Trash
* Automation
* Search

## Requirement
* PHP >= 8.0
* Sulu >= 2.5
* Symfony >= 5.4
* Composer

## Installation
### Install the bundle

Execute the following [composer](https://getcomposer.org/) command to add the bundle to the dependencies of your
project:

```bash
composer require pixelmairie/sulu-townhallbundle
```

### Enable the bundle

Enable the bundle by adding it to the list of registered bundles in the `config/bundles.php` file of your project:

 ```php
 return [
     /* ... */
     Pixel\TownHallBundle\TownHallBundle::class => ['all' => true],
 ];
 ```

### Update schema
```shell script
bin/console do:sch:up --force
```

## Bundle Config

Define the Admin Api Route in `routes_admin.yaml`
```yaml
townhall.settings_api:
  type: rest
  prefix: /admin/api
  resource: pixel_townhall.settings_route_controller
  name_prefix: townhall.

townhall.reports_api:
  type: rest
  prefix: /admin/api
  resource: pixel_townhall.reports_route_controller
  name_prefix: townhall.

townhall.bulletins_api:
  type: rest
  prefix: /admin/api
  resource: pixel_townhall.bulletins_route_controller
  name_prefix: townhall.

townhall.procedures_api:
  type: rest
  prefix: /admin/api
  resource: pixel_townhall.procedures_route_controller
  name_prefix: townhall.

townhall.flash_infos_api:
  type: rest
  prefix: /admin/api
  resource: pixel_townhall.flash-infos_route_controller
  name_prefix: townhall.

townhall.decrees_api:
  type: rest
  prefix: /admin/api
  resource: pixel_townhall.decrees_route_controller
  name_prefix: townhall.

townhall.publics_markets_api:
  type: rest
  prefix: /admin/api
  resource: pixel_townhall.publics_markets_route_controller
  name_prefix: townhall.

townhall.deiberations_api:
  type: rest
  prefix: /admin/api
  resource: pixel_townhall.deliberations_route_controller
  name_prefix: townhall.
``` 

## Use

This bundle has a lot of different parts:
* Flash info: to display quick information about the city (road block, weather, events, ...)
* Report: the reports of the municipal council
* Bulletin: town hall bulletins about the city life for example
* Procedure: the different procedures available in the town hall (ID card, passport, driving licence, ...)
* Decree: the municipal or prefectoral decrees
* Public market: the different public market (road working, housing construction) offered by the town hall
* Deliberation: the deliberations of the town hall

The following sections will present globally how to interact with the bundle as it is the same for each entity.
When needed, a more specific presentation will be done.

### Add/Edit
Go to the "Town hall" section in the administration interface. Then, click on the subsection you wish to intervene.
To add, simply click on "Add". Fill the fields that are needed for your use.

The fields depend on the subsection you want to edit.

Flash info:
* Title (mandatory)
* Cover (mandatory)
* PDF file
* Description (mandatory)

Report:
* Title
* Date (mandatory)
* PDF file
* Description

Bulletin:
* Title (mandatory)
* Date (mandatory)
* Cover
* PDF file
* Description

Procedure:
* Title (mandatory)
* URL (mandatory and filled automatically according to the title)
* Cover
* PDF file
* External link
* Category (mandatory)
* Description (mandatory)

Decree:
* Title (mandatory)
* Start date (mandatory)
* End date
* PDF file (mandatory)
* Type of decree (mandatory)
* Description

Public market:
* Title (mandatory)
* URL (mandatory and filled automatically according to the title)
* Published at (filled manually)
* Status (mandatory)
* Description (mandatory)
* List of documents

Deliberation:
* Title (mandatory)
* Date (mandatory)
* PDF file (mandatory)
* Description

Once you finished, click on "Save".

The entity you added is not visible on the website yet. In order to do that, click on "Activate?" or "Published?. It should be now visible for visitors.

To edit, simply click on the pencil at the left of the entity you wish to edit.

Some of these entities have a preview:
* Procedure
* Public market

### Categories
As you may have seen in the previous section, some entities need a category, a type or a status. These categories, types and status need to be created in a very specific way.

For the categories:
* You **must** create a root category which **must** have its key named "procedures"
* Then, under this root category, you create all the categories you need

For the type:
* You **must** create a root category which **must** have its key named "decrees"
* Then, under this root category, you create all the categories you need

For the status:
* You **must** create a root category which **must** have its key named "publics_markets"
* Then, under this root category, you create all the categories you need

### Remove/Restore

There are two ways to remove a town hall entity:
* Check every entity you want to remove and then click on "Delete"
* Go to the detail of an entity (see the "Add/Edit" section) and click on "Delete".

In both cases, the entity will be put in the trash.

To access the trash, go to the "Settings" and click on "Trash".
To restore an entity, click on the clock at the left. Confirm the restore. You will be redirected to the detail of the entity you restored.

To remove permanently an entity, check all the entities you want to remove and click on "Delete".

## Settings

This bundle comes with settings. To access the bundle settings, go to "Settings > Town hall management".

Here is the list of the different settings:
* Town hall name
* Weather

The weather setting is a textarea in which you can put HTML in order to use the weather service iframe.

Here is an example of how to use the settings:
```php
<?php

namespace Pixel\TownHallBundle\Controller\Website;

use Doctrine\ORM\EntityManagerInterface;
use Pixel\TownHallBundle\Entity\Setting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MeteoController extends AbstractController
{
    /**
     * @Route("meteo", name="meteo", options={"expose"=true}, methods={"POST"})
     */
    public function meteo(EntityManagerInterface $entityManager): JsonResponse
    {
        $setting = $entityManager->getRepository(Setting::class)->find(1);

        return new JsonResponse([
            "success" => true,
            "template" => $setting->getMeteo(),
        ]);
    }
}
```

## Contributing

You can contribute to this bundle. The only thing you must do is respect the coding standard we implement.
You can find them in the `ecs.php` file.
