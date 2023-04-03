<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test;

use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

/**
 * @Sonata\Admin (
 *     datagridFields={
 *          "id": @Sonata\DatagridField,
 *          "name": @Sonata\DatagridField,
 *     },
 *     formFields={
 *          "name": @Sonata\FormField
 *     },
 *     listFields={
 *          "id": @Sonata\ListField,
 *          "name": @Sonata\ListField,
 *     },
 *     showFields={
 *          "id": @Sonata\ShowField,
 *          "name": @Sonata\ShowField
 *     },
 *     exportFields={
 *          "id": @Sonata\ExportField,
 *          "name": @Sonata\ExportField
 *     },
 * )
 */
class TestAdminAnnotationFields
{

    public ?int $id = null;

    public ?string $name = null;

}