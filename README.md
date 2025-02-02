Sonata Annotation Bundle
============

Adds Annotations for Sonata Admin.

The point is to reduce noise, having lots of admin classes with just mappings
that don't do anything else should be avoided. Add annotations to your models
and you are done. If you need something that is not covered by this bundle,
create admin class instead.

This bundle is a fork of the archived [KunicMarko\SonataAnnotationBundle](https://github.com/kunicmarko20/SonataAnnotationBundle).

[![PHP Version](https://img.shields.io/badge/php-%5E7.4|%5E8.0-blue.svg)](https://circleci.com/gh/neimheadh/SonataAnnotationBundle)
[![Latest Stable Version](https://poser.pugx.org/neimheadh/sonata-annotation-bundle/v/stable)](https://packagist.org/packages/neimheadh/sonata-annotation-bundle)
[![Latest Unstable Version](https://poser.pugx.org/neimheadh/sonata-annotation-bundle/v/unstable)](https://packagist.org/packages/neimheadh/sonata-annotation-bundle)

[![Build Status](https://circleci.com/gh/neimheadh/SonataAnnotationBundle/tree/master.svg?style=shield)](https://circleci.com/gh/neimheadh/SonataAnnotationBundle)
[![Coverage Status](https://coveralls.io/repos/github/neimheadh/SonataAnnotationBundle/badge.svg?branch=master)](https://coveralls.io/github/neimheadh/SonataAnnotationBundle?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/neimheadh/SonataAnnotationBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/neimheadh/SonataAnnotationBundle/?branch=master)

Documentation
-------------

* [Installation](#installation)
* [Configuration](#configuration)
* [How to use](#how-to-use)
* [Annotations](#annotations)
    * [Admin](#admin)
    * [Access](#access)
    * [AddChild](#addChild)
    * [FormField](#formfield)
    * [ShowField](#showfield)
    * [ShowAssociationField](#showassociationfield)
    * [ListField](#listfield)
    * [ListAssociationField](#listassociationfield)
    * [DatagridField](#datagridfield)
    * [DatagridAssociationField](#datagridassociationfield)
    * [ExportField](#exportfield)
    * [ExportAssociationField](#exportassociationfield)
    * [ExportFormats](#exportformats)
    * [AddRoute](#addroute)
    * [RemoveRoute](#removeroute)
    * [ActionButton](#actionbutton)
    * [DashboardAction](#dashboardaction)
    * [ListAction](#listaction)
    * [DatagridValues](#datagridvalues)
* [Extending The Admin](#extending-the-admin)

## Installation

**1.**  Add dependency with composer

```bash
composer require neimheadh/sonata-annotation-bundle
```

**2.** Register the bundle in your Kernel

```php
return [
    //...
    Neimheadh\SonataAnnotationBundle\SonataAnnotationBundle::class => ['all' => true],
];
```

## Configuration

By default we scan all files in your `src` directory, if you save your entities somewhere
else you can change the directory:

```yaml
sonata_annotation:
    directory: '%kernel.project_dir%/src/'
```

## How to use

Instead of creating class for Admin it is enough to just add annotation
to your entity.

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

/**
 * @Sonata\Admin("Category")
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
    /**
     * @Sonata\FormField()
     * @Sonata\ListField()
     * @Sonata\ShowField()
     * @Sonata\DatagridField()
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
}
```

Clear cache:

```bash
bin/console cache:clear
```

And you will see Admin appear in your sidebar.

Note that if you can also use class attributes:

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

#[ORM\Table]
#[ORM\Entity]
#[Sonata\Admin("Category")]
class Category
{
    #[ORM\Column(name: "name", type: "string", length: 255)]
    #[Sonata\FormField()]
    #[Sonata\ListField()]
    #[Sonata\ShowField()]
    #[Sonata\DatagridField()]
    private $name;
}
```

## Annotations

### Admin

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;
use App\Controller\YourCRUDController;
use App\Admin\YourAdmin;

/**
 * @Sonata\Admin(
 *     label="Category",
 *     managerType="orm",
 *     group="Category",
 *     showInDashboard=true,
 *     keepOpen=true,
 *     onTop=true,
 *     icon="<i class='fa fa-user'></i>",
 *     labelTranslatorStrategy="sonata.admin.label.strategy.native",
 *     labelCatalogue="App",
 *     pagerType="simple",
 *     controller=YourCRUDController::class,
 *     serviceId="app.admin.category",
 *     admin=YourAdmin::class,
 *     code="admin_code",
*      defaultSort="id",
*      defaultPage=1,
*      defaultOrder="DESC",
*      defaultPageSize=10,
 * )
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
}

```

### Access

If you are using role handler as described [here](https://sonata-project.org/bundles/admin/3-x/doc/reference/security.html#role-handler)
you can add permission per role with this annotation.

>This annotation can be used without Admin annotation present. If you have an admin class for your entity
you can still use this annotation.

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

/**
 * @Sonata\Admin("Category")
 *
 * @Sonata\Access("ROLE_CLIENT", permissions={"LIST", "VIEW", "EXPORT"})
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
}

```

### AddChild

You can read more about this [here](https://docs.sonata-project.org/projects/SonataAdminBundle/en/4.x/reference/child_admin/).

>This annotation can be used without Admin annotation present. If you have an admin class for your entity
you can still use this annotation.

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

/**
 * @Sonata\Admin("Category")
 * @Sonata\AddChild(class=Post::class, field="category")
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
}
```

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

/**
 * @Sonata\Admin("Post")
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Post
{
    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;
}
```

### FormField

You can specify action option that would allow you to have different fields or
have different configuration for the same field for create and edit action.
If not set, field will be used for create and edit. Besides that, you are able
to set the position of the field. Position 1 would be the first field to 
render and higher numbers after. If field doesn't have position, it will be 
rendered after all fields with position.

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * @Sonata\Admin("Category")
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
    /**
     * @Sonata\FormField(
     *      action="create",
     *      type="",
     *      options={},
     *      fieldDescriptionOptions={},
     *      position=1 
     * )
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @Sonata\FormField(
     *      type="",
     *      options={},
     *      fieldDescriptionOptions={},
     *      position=2
     * )
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;
    
    /**
     * @Sonata\FormField(
     *      action="create",
     *      type=TextType::class
     * )
     *
     * @Sonata\FormField(
     *      action="edit",
     *      type=TextareaType::class
     * )
     *
     * @ORM\Column(name="something", type="string", length=255)
     */
    private $something;
}
```

Form fields can also be added on class level through `formFields` class `Admin` annotation property:

```php
// ...

/**
 * @Sonata\Admin(
 *    name="Category",
 *    formFields={
 *      "name": @Sonata\FormField (
 *          action="create",
 *          type="",
 *          options={},
 *          fieldDescriptionOptions={},
 *          position=1     
 *     ),
 *     "description": @Sonata\FormField (
 *          type="",
 *          options={},
 *          fieldDescriptionOptions={},
 *          position=2
 *     ),
 * )
 */
class Category {
//...
}
```

By default, if any field configured, all field will be available.

### ShowField

You are able to set the position of the field. Position 1 would be the first
field to render and higher numbers after. If field doesn't have position, it
will be rendered after all fields with position.

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

/**
 * @Sonata\Admin("Category")
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
    /**
     * @Sonata\ShowField(
     *      type="",
     *      fieldDescriptionOptions={},
     *      position=1
     * )
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @Sonata\ShowField()
     */
    public function showThis(): string
    {
        return 'show value';
    }
}
```

Show fields can also be added on class level through `showFields` class `Admin` annotation property:

```php
<?php
// ...

/**
 * @Sonata\Admin(
 *     name="Category",
 *     showFields={
 *         "name": @Sonata\ShowField(
 *             type="",
 *             fieldDescriptionOptions={},
 *             position=1
 *         )
 *     }
 * )
 */
class Category
{
  // ...
}
```

By default, if any field configured, all field will be available.

### ShowAssociationField

You are able to set the position of the field. Position 1 would be the first
field to render and higher numbers after. If field doesn't have position, it
will be rendered after all fields with position.

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

/**
 * @Sonata\Admin("Category")
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
    /**
     * @Sonata\ShowAssociationField(
     *      field="name",
     *      type="",
     *      fieldDescriptionOptions={},
     *      position=1
     * )
     *
     * @Sonata\ShowAssociationField(
     *      field="email",
     *      type="",
     *      fieldDescriptionOptions={},
     *      position=1
     * )
     *
     * @ORM\ManyToOne(targetEntity="Owner")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;
}
```

### ListField

You are able to set the position of the field. Position 1 would be the first
field to render and higher numbers after. If field doesn't have position, it
will be rendered after all fields with position.

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

/**
 * @Sonata\Admin("Category")
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
    /**
     * @Sonata\ListField(identifier=true) 
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Sonata\ListField(
     *      type="",
     *      fieldDescriptionOptions={},
     *      identifier=false,
     *      position=1
     * )
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @Sonata\ListField(position=1)
     */
    public function listThis(): string
    {
        return 'list value';
    }
}
```

List fields can also be added on class level through `listFields` class `Admin` annotation property:

```php
// ...

/**
 * @Sonata\Admin(
 *   listFields= {
 *       "id": @Sonata\ListField(identifier=true), 
 *       "name": @Sonata\ListField(
 *           type="",
 *           fieldDescriptionOptions={},
 *           identifier=false,
 *           position=1
 *       )
 *   }
 * )
 */
class Category {
// ...
}
```

By default, if any field configured, all field will be available.

### ListAssociationField

You are able to set the position of the field. Position 1 would be the first
field to render and higher numbers after. If field doesn't have position, it
will be rendered after all fields with position.

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

/**
 * @Sonata\Admin("Category")
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
    /**
     * @Sonata\ListAssociationField(
     *      field="name",
     *      type="",
     *      fieldDescriptionOptions={},
     *      identifier=false,
     *      position=1
     * )
     * 
     * @Sonata\ListAssociationField(
     *      field="email",
     *      type="",
     *      fieldDescriptionOptions={},
     *      identifier=false
     * )
     *
     * @ORM\ManyToOne(targetEntity="Owner")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;
}
```


### DatagridField

You are able to set the position of the field. Position 1 would be the first
field to render and higher numbers after. If field doesn't have position, it
will be rendered after all fields with position.

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

/**
 * @Sonata\Admin("Category")
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
    /**
     * @Sonata\DatagridField(
     *      type="",
     *      fieldDescriptionOptions={},
     *      filterOptions={},
     *      fieldType="",
     *      fieldOptions={},
     *      position=1
     * )
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
}
```

List fields can also be added on class level through `datagridFields` class `Admin` annotation property:

```php
// ...

/**
 * @Sonata\Admin(
 *     datagridFields= {
 *         "name": @Sonata\DatagridField (
 *             type="",
 *             fieldDescriptionOptions={},
 *             filterOptions={},
 *             fieldType="",
 *             fieldOptions={},
 *             position=1 
 *         )     
 *     }   
 * )
 */
class Category {
// ...
}
```

By default, if any field configured, all field will be available.

### DatagridAssociationField

You are able to set the position of the field. Position 1 would be the first
field to render and higher numbers after. If field doesn't have position, it
will be rendered after all fields with position.

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

/**
 * @Sonata\Admin("Category")
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
    /**
     * @Sonata\DatagridAssociationField(
     *      field="name",
     *      type="",
     *      fieldDescriptionOptions={},
     *      filterOptions={},
     *      fieldType="",
     *      fieldOptions={},
     *      position=1
     * )
     *
     * @Sonata\DatagridAssociationField(
     *      field="email",
     *      type="",
     *      fieldDescriptionOptions={},
     *      filterOptions={},
     *      fieldType="",
     *      fieldOptions={}
     * )
     *
     * @ORM\ManyToOne(targetEntity="Owner")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;
}
```

### ExportField

You can add annotation to fields/method that you want to export, also you can add
label for the field, if left blank field name will be used as label.

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

/**
 * @Sonata\Admin("Category")
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
    /**
     * @Sonata\ExportField()
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @Sonata\ExportField("Custom Name")
     *
     * @ORM\Column(name="tag", type="string", length=255)
     */
    private $tag;

    /**
     * @Sonata\ExportField()
     */
    public function exportThis(): string
    {
        return 'export value';
    }
}
```

By default, if any field configured, all field will be available.

### ExportAssociationField

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

/**
 * @Sonata\Admin("Category")
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
    /**
     * @Sonata\ExportAssociationField(
     *      field="name",
     *      label="Owner"
     * )
     *
     * @Sonata\ExportAssociationField(
     *      field="email",
     *      label="Email"
     * )
     *
     * @ORM\ManyToOne(targetEntity="Owner")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;
}
```

### ExportFormats

You can customize the export formats you want to allow, if this annotation
is not present, all formats are shown.

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

/**
 * @Sonata\Admin("Category")
 *
 * @Sonata\ExportFormats({"json", "xml"})
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
    /**
     * @Sonata\ExportField()
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
}
```

### AddRoute

Add custom routes to your admin class:

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;
use App\Controller\YourCRUDController;

/**
 * @Sonata\Admin(
 *     label="Category",
 *     controller=YourCRUDController::class
 * )
 *
 * @Sonata\AddRoute("import")
 * @Sonata\AddRoute(name="send_mail", path="{id}/send_mail")
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
}
```

### RemoveRoute

remove already existing routes:

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;
use App\Controller\YourCRUDController;

/**
 * @Sonata\Admin(
 *     label="Category",
 *     controller=YourCRUDController::class
 * )
 *
 * @Sonata\RemoveRoute("edit")
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
}
```

### ActionButton

This will add button next to your add button in a list view. [Here](https://docs.sonata-project.org/projects/SonataAdminBundle/en/4.x/reference/dashboard/#configuring-what-actions-are-available-for-each-item-on-the-dashboard)
you can find how the template should look like.

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;
use App\Controller\YourCRUDController;

/**
 * @Sonata\Admin(
 *     label="Category",
 *     controller=YourCRUDController::class
 * )
 *
 * @Sonata\AddRoute(name="import", path="/import")
 *
 * @Sonata\ActionButton("import_action_button.html.twig")
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
}
```

### DashboardAction

This will add button to your dashboard block for this entity. [Here](https://sonata-project.org/bundles/admin/3-x/doc/cookbook/recipe_custom_action.html#custom-action-without-entity)
you can find how the template should look like.

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;
use App\Controller\YourCRUDController;

/**
 * @Sonata\Admin(
 *     label="Category",
 *     controller=YourCRUDController::class
 * )
 *
 * @Sonata\AddRoute(name="import", path="/import")
 *
 * @Sonata\DashboardAction("import_dashboard_button.html.twig")
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
}
```

### ListAction

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;
use App\Controller\YourCRUDController;

/**
 * @Sonata\Admin(
 *     label="Category",
 *     controller=YourCRUDController::class
 * )
 *
 * @Sonata\AddRoute(name="import", path="/import")
 *
 * @Sonata\ListAction("show")
 * @Sonata\ListAction("edit")
 * @Sonata\ListAction("delete")
 * @Sonata\ListAction(name="import", options={"template"="import_list_button.html.twig"})
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
}
```

### DatagridValues

As explained [here](https://symfony.com/doc/master/bundles/SonataAdminBundle/reference/action_list.html#configure-the-default-ordering-in-the-list-view).
```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;
use App\Controller\YourCRUDController;

/**
 * @Sonata\Admin("Category")
 *
 * @Sonata\DatagridValues({"_sort_by":"p.name"})
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
}
```

### Extending The Admin

Sometimes you need to do something custom and this bundle can't help you with
that but you still want to use annotations for most of the other stuff. You can
extend our admin class `Neimheadh\SonataAnnotationBundle\Admin\AnnotationAdmin`
and overwrite the methods you want.

```php
<?php

namespace App\Admin;

use Neimheadh\SonataAnnotationBundle\Admin\AnnotationAdmin;

class YourAdmin extends AnnotationAdmin
{
    //do what you want
}
```

And then in your entity you just provide that class

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;
use App\Admin\YourAdmin;

/**
 * @Sonata\Admin(
 *     label="Category",
 *     admin=YourAdmin::class
 * )
 *
 * @ORM\Table
 * @ORM\Entity
 */
class Category
{
}
```
