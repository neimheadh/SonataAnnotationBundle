<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Annotation;

/**
 * List association field annotation.
 *
 * Allows you to configure your list field having an association field.
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class ListAssociationField extends ListField
{
    /**
     * Association field name.
     *
     * @var string
     */
    public string $field;
}
