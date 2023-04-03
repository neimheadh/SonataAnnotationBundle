<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test;

use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

#[Sonata\Admin(
    datagridFields: ['id' => new Sonata\DatagridField(), 'name' => new Sonata\DatagridField()],
    formFields: [
        'name' => new Sonata\FormField(),
        'title' => new Sonata\FormField(action: Sonata\FormField::ACTION_EDIT),
        'label' => new Sonata\FormField(action: Sonata\FormField::ACTION_CREATE),
    ],
    listFields: ['id' => new Sonata\ListField(), 'name' => new Sonata\ListField()],
    showFields: ['id' => new Sonata\ShowField(), 'name' => new Sonata\ShowField()],
    exportFields: ['id' => new Sonata\ExportField(), 'name' => new Sonata\ExportField()]
)]
class TestAdminAnnotationFieldsAttribute
{

    public ?int $id = null;

    public ?string $name = null;

    public ?string $title = null;

    public ?string $label = null;

}